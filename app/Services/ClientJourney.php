<?php

namespace App\Services;

use App\Models\Document;
use App\Models\User;
use App\Models\UserSubscription;
use App\Models\WompiOrder;

class ClientJourney
{
    /** Orientación de lectura: nunca activa, paga ni actualiza un proyecto. */
    public function forUser(User $user): array
    {
        abort_unless($user->isClient(), 403);
        $step = [
            'kind'=>'choose', 'active'=>false, 'status'=>'Tu siguiente paso',
            'title'=>'Elige cómo empezar',
            'description'=>'Consulta el alcance, los plazos y las condiciones de cada paquete para elegir el que se ajusta a tu proyecto.',
            'client'=>'Elige el servicio que necesitas y revisa sus condiciones.',
            'team'=>'El acompañamiento comienza cuando el servicio está confirmado y has compartido tus insumos.',
            'action'=>'Explorar servicios', 'url'=>route('private.planes'),
            'plan'=>null, 'until'=>null,
        ];
        $user->loadMissing('activePaidSubscription.paymentPlan');
        $subscription=$user->activePaidSubscription;
        if (!$subscription) {
            // Una orden adicional no sustituye la contratación del servicio principal.
            $order=WompiOrder::where('user_id',$user->id)
                ->where('environment',config('wompi.environment'))
                ->where('is_additional',false)->latest('id')->first();
            if ($order && $order->status==='PENDING' && ($order->expires_at?->isFuture() || $order->transaction_id)) {
                $sandbox=$order->environment==='sandbox';
                return array_replace($step,[
                    'kind'=>'payment_pending','status'=>$sandbox?'Pago de prueba pendiente':'Pago pendiente de confirmación',
                    'title'=>'Comprueba el estado de tu pago',
                    'description'=>$sandbox?'Este pago es simulado. Su aprobación no habilita un servicio real.':'Tu pago aún no está confirmado. La carga se habilita cuando el pago y el período del servicio están vigentes.',
                    'client'=>'Consulta esta orden antes de iniciar otro intento de pago.',
                    'team'=>'El trabajo con documentos comienza con el servicio activo y los insumos recibidos.',
                    'action'=>'Ver estado del pago','url'=>route('private.payments.show',$order),
                ]);
            }
            $scheduled=UserSubscription::where('user_id',$user->id)
                ->whereIn('payment_status',['paid','pagado','approved','aprobado','completed','completado'])
                ->whereIn('status',['active','vigente'])->whereDate('start_date','>',now()->toDateString())
                ->whereColumn('end_date','>=','start_date')->orderBy('start_date')->first();
            if ($scheduled) {
                return array_replace($step,[
                    'kind'=>'scheduled','status'=>'Período de trabajo programado',
                    'title'=>'Prepara los insumos para comenzar',
                    'description'=>'El período de carga de tu servicio comienza el '.$scheduled->start_date->format('d/m/Y').'. Mientras tanto, reúne tu manuscrito y los requisitos de revisión.',
                    'client'=>'Prepara los archivos y las indicaciones para compartirlos cuando se habilite el acceso.',
                    'team'=>'El trabajo y los ajustes se desarrollan dentro del período acordado.',
                    'action'=>'Consultar mi servicio',
                ]);
            }
            $expired=UserSubscription::where('user_id',$user->id)
                ->whereIn('payment_status',['paid','pagado','approved','aprobado','completed','completado'])
                ->whereIn('status',['active','vigente'])->whereDate('end_date','<',now()->toDateString())
                ->latest('end_date')->first();
            if ($expired) {
                return array_replace($step,[
                    'kind'=>'expired','status'=>'Período de carga finalizado',
                    'title'=>'Consulta las condiciones de tu servicio',
                    'description'=>'La carga de documentos está vinculada al período de acompañamiento o trabajo y ajustes acordado. Revisa tu servicio antes de enviar nuevos archivos.',
                    'client'=>'Consulta tu servicio para revisar la continuidad del trabajo.',
                    'team'=>'El equipo mantiene el seguimiento según las condiciones y el período acordados.',
                    'action'=>'Consultar mi servicio',
                ]);
            }
            if ($order && $order->status==='APPROVED' && $order->environment==='production') {
                return array_replace($step,[
                    'kind'=>'activation','status'=>'Pago confirmado',
                    'title'=>'Consulta la activación de tu servicio',
                    'description'=>'Tu pago consta aprobado. La carga requiere también un período de servicio vigente; consulta el estado antes de repetir un pago.',
                    'client'=>'Revisa el estado de tu servicio y sus fechas.',
                    'team'=>'La habilitación debe corresponder al servicio y al período acordados.',
                    'action'=>'Ver estado del pago','url'=>route('private.payments.show',$order),
                ]);
            }
            return $step;
        }
        $upload=route('private.upload-document');
        $step=array_replace($step,[
            'active'=>true,'plan'=>$subscription->paymentPlan?->name,
            'until'=>$subscription->end_date?->format('d/m/Y'),
            'kind'=>'prepare','status'=>'Servicio activo',
            'title'=>'Comparte tu manuscrito',
            'description'=>'Tu acceso está habilitado. Reúne los archivos y las indicaciones que orientarán el trabajo.',
            'client'=>'Envía el manuscrito, los materiales o las indicaciones que necesitas revisar.',
            'team'=>'Con tus insumos, el equipo organiza el trabajo y coordina contigo la orientación inicial.',
            'action'=>'Preparar mi envío','url'=>$upload.'#sp-upload-files-title',
        ]);
        $submitted=Document::where('user_id',$user->id)->where('uploaded_by',$user->id)->exists()
            || $user->additionalInformationEntries()->isNotEmpty();
        if (!$submitted) return $step;
        $assignment=app(ProjectAccess::class)->ready() ? $user->projectAssignment()->first() : null;
        $status=$assignment?->progress_status ?? 'received';
        $returned=Document::where('user_id',$user->id)->where('uploaded_by','!=',$user->id)->exists();
        if ($status==='delivered') {
            return array_replace($step,[
                'kind'=>'delivered','status'=>'Proyecto entregado',
                'title'=>$returned?'Consulta tu entrega':'Consulta el cierre de tu proyecto',
                'description'=>$returned?'El expediente incluye documentos compartidos por el equipo. Revisa la versión entregada y los mensajes de cierre.':'El equipo marcó el proyecto como entregado. Consulta los mensajes para conocer el cierre y las indicaciones.',
                'client'=>'Revisa lo entregado y comparte observaciones dentro del período y alcance acordados.',
                'team'=>'El seguimiento posterior depende del servicio contratado y de sus condiciones.',
                'action'=>$returned?'Ver documentos entregados':'Ver mensajes del equipo',
                'url'=>$upload.($returned?'#sp-upload-library-title':'#sp-upload-followup-title'),
            ]);
        }
        if ($status==='adjustments') {
            return array_replace($step,[
                'kind'=>'adjustments','status'=>'Proyecto en ajustes','title'=>'Revisa las observaciones',
                'description'=>'Consulta las devoluciones y los mensajes para orientar los ajustes del manuscrito.',
                'client'=>'Revisa las observaciones y envía comentarios o insumos para los ajustes.',
                'team'=>'El equipo trabaja en los ajustes según el intercambio y el alcance acordados.',
                'action'=>'Consultar ajustes','url'=>$upload.'#sp-upload-followup-title',
            ]);
        }
        if ($status==='in_review') {
            return array_replace($step,[
                'kind'=>'in_review','status'=>'Proyecto en revisión','title'=>'Sigue el avance de tu manuscrito',
                'description'=>'El proyecto está marcado en revisión. Los mensajes y las devoluciones se consultan en tu expediente.',
                'client'=>'Consulta el seguimiento y comparte información adicional cuando sea necesaria.',
                'team'=>'El equipo revisa y desarrolla el manuscrito según el servicio y los insumos recibidos.',
                'action'=>'Ver seguimiento','url'=>$upload.'#sp-upload-followup-title',
            ]);
        }
        return array_replace($step,[
            'kind'=>'received','status'=>'Insumos recibidos','title'=>'Tu proyecto ya tiene insumos',
            'description'=>'Tus archivos o indicaciones están registrados en el expediente. Consulta allí la asignación y los mensajes.',
            'client'=>'Revisa lo enviado y completa los insumos que ayuden a orientar el trabajo.',
            'team'=>'El equipo organiza el inicio y coordina contigo los siguientes pasos.',
            'action'=>'Consultar mi expediente','url'=>$upload.'#sp-upload-followup-title',
        ]);
    }
}
