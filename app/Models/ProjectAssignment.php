<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectAssignment extends Model {
    protected $fillable=['client_id','advisor_id','assigned_by','project_title','progress_status'];
    public const STATUSES=['received'=>'Recibido','in_review'=>'En revisión','adjustments'=>'En ajustes','delivered'=>'Entregado'];
    public function advisor(): BelongsTo { return $this->belongsTo(User::class,'advisor_id')->where('role','advisor')->where('status','active'); }
    public function client(): BelongsTo { return $this->belongsTo(User::class,'client_id'); }
}
