<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\UserResource;
use App\Mail\ForgotPassword;
use App\Models\Group;
use App\Models\PasswordReset;
use App\Models\Permission;
use App\Models\Section;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function authenticate(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::query()->where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'The provided credentials are incorrect.',
            ], 401);
        }

        $loggedInUser = $user;
        $loggedInUser->last_login = date('Y-m-d H:i:s');
        $loggedInUser->login_count = $loggedInUser->login_count + 1;
        $loggedInUser->save();

        $user->tokens()->where('name', $request->header('user-agent').' '.$request->ip())->delete();

        $return = [
            'token' => $user->createToken($request->header('user-agent').' '.$request->ip())->plainTextToken,
            'user' => new UserResource($user),
        ];

        return response()->json($return);
    }

    public function getAuthenticatedUser(): JsonResponse
    {
        return response()->json(new UserResource(auth()->user()));
    }

    public function forgotPassword(Request $request): ?JsonResponse
    {
        $user = User::query()->where('email', $request->get('email'))->first();

        if ($user) {
            $token = Str::random(40);

            PasswordReset::query()->create([
                'user_id' => $user->id,
                'email' => $user->email,
                'token' => $token,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ]);

            Mail::to($user->email)->queue(new ForgotPassword($user, $token));

            return response()->json([]);
        }

        return null;
    }

    public function checkToken(): JsonResponse
    {
        return response()->json(['success' => $this->isResetTokenValid(request('token'))]);
    }

    public function resetPassword(): JsonResponse
    {
        if (!$this->isResetTokenValid(request('token'))) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid password reset code'
            ], Response::HTTP_NOT_FOUND);
        }

        if (strlen(request('password')) < 6) {
            return response()->json([
                'success' => false,
                'message' => 'Password length must be at least 6 characters'
            ], Response::HTTP_NOT_ACCEPTABLE);
        }

        $token = PasswordReset::query()->where('token', request('token'))->first();
        $user = $token->user;

        $user->password = request('password');
        $user->save();

        $token->delete();

        return response()->json([
            'success' => true,
            'message' => 'Password has been updated.'
        ]);
    }

    private function isResetTokenValid(string $token): bool
    {
        $resetToken = PasswordReset::query()->where('token', $token)->first();

        return !($resetToken == null || $resetToken->count() <= 0);
    }

    public function googleCallback(Request $request): JsonResponse
    {
        if ($request->get('code')) {
            $response = Http::post('https://oauth2.googleapis.com/token', [
                'code' => $request->get('code'),
                'client_id' => env('GOOGLE_CLIENT_ID'),
                'client_secret' => env('GOOGLE_CLIENT_SECRET'),
                'redirect_uri' => env('GOOGLE_REDIRECT_URL'),
                'grant_type' => 'authorization_code',
            ]);

            if ($response) {
                $user = User::query()->find(auth()->user()->id);
                $user->google_access_token = $response['access_token'];

                if ($response['refresh_token']) {
                    $user->google_refresh_token = $response['refresh_token'];
                }

                $user->save();
            }

            return response()->json(['success' => true, 'message' => 'Google Authentication Completed Successfully.']);
        } else {
            return response()->json(['success' => false, 'message' => 'Google Authentication Failed.'], 401);
        }
    }

    public function microsoftCallback(Request $request): JsonResponse
    {
        if ($request->get('code')) {
            $response = Http::asForm()->post('https://login.microsoftonline.com/common/oauth2/v2.0/token', [
                'code' => $request->get('code'),
                'grant_type' => 'authorization_code',
                'client_id' => env('MICROSOFT_CLIENT_ID'),
                'client_secret' => env('MICROSOFT_CLIENT_SECRET'),
                'redirect_uri' => env('MICROSOFT_REDIRECT_URL'),
                'scope' => 'https://graph.microsoft.com/offline_access https://graph.microsoft.com/User.Read https://graph.microsoft.com/Mail.ReadWrite https://graph.microsoft.com/Mail.Send https://graph.microsoft.com/Mail.ReadWrite.Shared https://graph.microsoft.com/Mail.Send.Shared https://graph.microsoft.com/Calendars.ReadWrite offline_access',
            ]);

            if ($response) {
                $user = User::query()->find(auth()->user()->id);
                $user->microsoft_access_token = $response['access_token'];

                if ($response['refresh_token']) {
                    $user->microsoft_refresh_token = $response['refresh_token'];
                }

                $graph = new Graph();
                $graph->setAccessToken($user->microsoft_access_token);

                try {
                    $microsoftUser = $graph->createRequest('GET', '/me')
                        ->setReturnType(Model\User::class)
                        ->execute();

                    $user->microsoft_account_email = $microsoftUser->getMail();
                    $user->microsoft_account_name = $microsoftUser->getDisplayName();
                } catch (GraphException | GuzzleException) {
                    return response()->json(['success' => false, 'message' => 'Microsoft Authentication Failed.'], 401);
                }

                $user->save();
            }

            return response()->json(['success' => true, 'message' => 'Microsoft Authentication Completed Successfully.']);
        } else {
            return response()->json(['success' => false, 'message' => 'Microsoft Authentication Failed.'], 401);
        }
    }

    public function xeroCallback(Request $request): JsonResponse
    {
        if ($request->get('code')) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Basic '.base64_encode(env('XERO_CLIENT_ID').':'.env('XERO_CLIENT_SECRET')),
                ])->asForm()->post('https://identity.xero.com/connect/token', [
                    'code' => $request->get('code'),
                    'grant_type' => 'authorization_code',
                    'redirect_uri' => env('XERO_REDIRECT_URL'),
                ]);
            } catch (Exception) {
                return response()->json(['success' => false, 'message' => 'Xero Authentication Failed.'], 401);
            }

            if ($response && isset($response['access_token'])) {
                $tenants = Http::withHeaders([
                    'Authorization' => 'Bearer '.$response['access_token'],
                ])->get('https://api.xero.com/connections');

                if (count(json_decode($tenants)) === 1) {
                    $tenantID = $tenants[0]['tenantId'];
                } else {
                    $tenantID = null;
                }

                Storage::disk('local')->put('xero.json', json_encode([
                    'access_token'  => $response['access_token'],
                    'refresh_token' => $response['refresh_token'],
                    'id_token'      => $response['id_token'],
                    'expires'       => $response['expires_in'],
                    'tenant_id'     => $tenantID,
                ]));

                return response()->json(['success' => true, 'message' => 'Xero Authentication Completed Successfully.', 'tenants' => json_decode($tenants)]);
            } else {
                return response()->json(['success' => false, 'message' => 'Xero Authentication Failed.'], 401);
            }
        }

        return response()->json(['success' => false, 'message' => 'Xero Authentication Failed.'], 401);
    }

    public static function getAccessControl($superAdmin = false, $user = null, $combined = true): array
        {
            $return = [];

            if ($superAdmin) { // User is a Super Admin
                $return['permissions'] = Permission::query()->select(['id', 'name', 'route'])->where('active', 1)->get()->toArray();
                $return['groups'] = Group::query()->select(['id', 'name'])->where('active', 1)->get()->toArray();
                $return['sections'] = Section::query()->select(['id', 'name', 'route'])->where('active', 1)->get()->toArray();
            } else {
                $return['groups'] = $user->groups()->select('id', 'name')->get()->toArray();
                $groupIDs = $user->groups()->get()->pluck('id')->toArray();

                $userPermissions = $user->permissions()->select(['id', 'name', 'route'])->where('active', 1)->get()->toArray();
                $userSections = $user->sections()->select(['id', 'name', 'route'])->where('active', 1)->get()->toArray();

                $groupPermissions = Permission::query()->select(['id', 'name', 'route'])
                ->whereHas('groups', function ($query) use ($groupIDs) {
                    $query->whereIn('id', $groupIDs);
                })->get()->toArray();

                $groupSections = Section::query()->select(['id', 'name', 'route'])
                ->whereHas('groups', function ($query) use ($groupIDs) {
                    $query->whereIn('id', $groupIDs);
                })->get()->toArray();

                if ($combined) {
                    $return['permissions'] = array_merge($userPermissions, $groupPermissions);
                    $return['sections'] = array_merge($userSections, $groupSections);
                } else {
                    $return['permissions'] = $userPermissions;
                    $return['sections'] = $userSections;
                }
            }

            $return['menu'] = self::getUserMenu($return['sections']);

            return $return;
        }

    public static function getUserMenu($sections): array
    {
        $ids = [];

        foreach ($sections as $section) {
            $ids[] = $section['id'];
        }

        return self::getMenuItemChildren($ids, null);
    }

    public static function getMenuItemChildren($ids, $id): array
    {
        $menuItems = [];

        if ($sections = Section::query()->whereIn('id', $ids)
            ->where('parent_id', $id)
            ->where('active', true)
            ->orderBy('ordering')->get()
        ){
            foreach ($sections as $section) {
                $menuItems[] = [
                    'title' => $section->name,
                    'icon' => $section->icon,
                    'route' => $section->route,
                    'children' => self::getMenuItemChildren($ids, $section->id),
                    'permission' => $section->permission,
                ];
            }
        }

        return $menuItems;
    }

    public function register(Request $request): JsonResponse
    {
        try {
            $member = new Member;
            $member->fill($request->all());
            $member->save();

            $return = [
                'success' => true,
                'token' => $member->createToken($request->header('user-agent').' '.$request->ip())->plainTextToken
            ];

            return response()->json($return);
        } catch(QueryException $e){
            if ($e->getCode() === '23000'){
                return response()->json(['success' => false, 'message' => 'A user with that email already exists.']);
            }

            return response()->json(['success' => false, 'message' => 'There was an error registering your account.']);
        }


    }
}
