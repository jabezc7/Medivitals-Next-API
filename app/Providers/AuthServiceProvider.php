<?php

namespace App\Providers;

use App\Models\Attachment;
use App\Models\Group;
use App\Models\Permission;
use App\Models\Section;
use App\Models\Type;
use App\Models\Note;
use App\Models\User;
use App\Models\Device;
use App\Models\Template;
use App\Models\Patient;
use App\Models\Notification;
use App\Models\Automation;
/* INSERT MODEL USE HERE */
use App\Policies\AttachmentPolicy;
use App\Policies\GroupPolicy;
use App\Policies\PermissionPolicy;
use App\Policies\SectionPolicy;
use App\Policies\TypePolicy;
use App\Policies\NotePolicy;
use App\Policies\UserPolicy;
use App\Policies\DevicePolicy;
use App\Policies\TemplatePolicy;
use App\Policies\PatientPolicy;
use App\Policies\NotificationPolicy;
use App\Policies\AutomationPolicy;
/* INSERT POLICY USE HERE */
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
		Attachment::class => AttachmentPolicy::class,
        User::class => UserPolicy::class,
        Group::class => GroupPolicy::class,
        Permission::class => PermissionPolicy::class,
        Section::class => SectionPolicy::class,
        Type::class => TypePolicy::class,
        Note::class => NotePolicy::class,
        Device::class => DevicePolicy::class,
		Template::class => TemplatePolicy::class,
		Patient::class => PatientPolicy::class,
		Notification::class => NotificationPolicy::class,
		Automation::class => AutomationPolicy::class,
		/* INSERT POLICY HERE */
        /* IMPORTANT - Leave the line above */
    ];

    public function boot()
    {
        $this->registerPolicies();
    }
}
