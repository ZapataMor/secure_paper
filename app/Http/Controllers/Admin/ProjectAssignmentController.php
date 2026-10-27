<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProjectAssignment;
use App\Models\User;
use App\Services\ProjectAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProjectAssignmentController extends Controller {
    public function store(Request $request,User $user,ProjectAccess $access): RedirectResponse {
        abort_unless($user->isClient(),404);
        abort_unless($access->ready(),503,'La asignación de proyectos aún no está instalada.');
        $input=$request->validate([
            'project_title'=>['required','string','max:160'],
            'advisor_id'=>['nullable','integer',Rule::exists('users','id')->where('role','advisor')->where('status','active')->whereNull('deleted_at')],
            'progress_status'=>['required',Rule::in(array_keys(ProjectAssignment::STATUSES))],
        ]);
        DB::transaction(function()use($user,$request,$input): void {
            User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            $before=ProjectAssignment::where('client_id',$user->id)->first();
            $previous=$before?->only(['advisor_id','project_title','progress_status']);
            $assignment=ProjectAssignment::updateOrCreate(['client_id'=>$user->id],[
                'project_title'=>trim($input['project_title']),
                'advisor_id'=>$input['advisor_id']??null,'progress_status'=>$input['progress_status'],
                'assigned_by'=>$request->user()->id,
            ]);
            DB::table('user_activities')->insert(['user_id'=>$request->user()->id,'action'=>'project_assignment_updated','module'=>'projects',
                'description'=>json_encode(['client_id'=>$user->id,'before'=>$previous,'after'=>$assignment->only(['advisor_id','project_title','progress_status'])],JSON_UNESCAPED_UNICODE),'created_at'=>now()]);
            if (($previous['advisor_id'] ?? null) != $assignment->advisor_id) {
                if (config('project_notifications.enabled')) app(\App\Services\ProjectEmailNotices::class)->schedule($user->id, 'assignment');
            } elseif (($previous['progress_status'] ?? null) !== $assignment->progress_status) {
                if (config('project_notifications.enabled')) app(\App\Services\ProjectEmailNotices::class)->schedule($user->id, 'progress');
            }
        });
        return redirect()->route('admin.works.show',$user)->with('status','Asignación y seguimiento del proyecto guardados.');
    }
    public function progress(Request $request,User $user,ProjectAccess $access): RedirectResponse {
        abort_unless($user->isClient(),404);
        abort_unless($access->allows($request->user(),$user->id),403);
        $input=$request->validate(['progress_status'=>['required',Rule::in(array_keys(ProjectAssignment::STATUSES))]]);
        DB::transaction(function()use($request,$user,$access,$input): void {
            User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            abort_unless($access->allows($request->user(),$user->id),403);
            $assignment=ProjectAssignment::where('client_id',$user->id)->firstOrFail();
            $previous=$assignment->progress_status;
            $assignment->update(['progress_status'=>$input['progress_status']]);
            DB::table('user_activities')->insert(['user_id'=>$request->user()->id,'action'=>'project_progress_updated','module'=>'projects',
                'description'=>json_encode(['client_id'=>$user->id,'before'=>$previous,'after'=>$input['progress_status']]),'created_at'=>now()]);
            if ($previous !== $input['progress_status']) if (config('project_notifications.enabled')) app(\App\Services\ProjectEmailNotices::class)->schedule($user->id, 'progress');
        });
        return redirect()->route('admin.works.show',$user)->with('status','Estado del proyecto actualizado.');
    }
}
