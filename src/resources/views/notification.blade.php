@extends('felaraframe::layout')
@php
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/css/style.css'),'headerstyles');
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/css/ui.css'),'headerstyles');
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/plugins/noty/noty.css'),'headerstyles');
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/plugins/noty/themes/bootstrap-v3.css'),'headerstyles');
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/plugins/noty/noty.min.js'),'footerscripts');
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/plugins/mcustom-scrollbar/jquery.mCustomScrollbar.concat.min.js'),'footerscripts');
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/js/application.js'),'footerscripts');
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/js/laraframe.js'),'footerscripts');
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/js/global.js'),'footerscripts');
    
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/plugins/quicksearch/quicksearch.min.js'),'footerscripts');
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/js/mailbox.js'),'footerscripts');
    
@endphp


@section('content')
@fePortlet([
    'id'=>'fe_notification',
    'class'=>'h-100p m-b-0 fullHeight',
    'attr'=>'MailTargt="'.route('LF_Notifications').'"'
    ])
    <div class="row h-100p">
        <div class="col-sm-12 page-app mailbox h-100p">
            <section class="app">
                <aside class="aside-md emails-list">
                    <section>
                        <div class="mailbox-page clearfix">
                            <h1 class="pull-left">Site Notifications</h1>
                            <div class="append-icon">
                                <input type="text" class="form-control form-white pull-right" id="email-search" placeholder="Search...">
                                <i class="icon-magnifier"></i>
                            </div>
                        </div>
            
                        <ul class="nav nav-tabs text-right">
                            <li class="active f-right"><a href="#recent" data-toggle="tab">Recent</a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade in active" id="recent">
                                <div class="messages-list withScroll show-scroll" data-padding="180" data-height="window">
                                    @foreach ($mails as $mail)
                                        <div class="message-item media" mail_id="{{$mail->id}}">
                                            <div class="media">
                                                <img src="{{asset('/feiron/felaraframe/images/shopping/mail.png')}}" alt="Mail Icon" width="40" class="sender-img">
                                                <div class="media-body">
                                                    <div class="sender">{{$mail->Sender->name??'From System'}}</div>
                                                    <div class="subject">{!!(($mail->sender<=0)?'<span class="label label-primary">System</span>':'').(empty($mail->remarks)?"":'<span class="label label-info">'.$mail->remarks.'</span>') !!} <span class="subject-text">{{$mail->subject}}</span></div>
                                                    <div class="date"><strong>{{$mail->created_at->format('(D)M-d Y')}}</strong></div>
                                                    <div class="email-content">
                                                        {{Illuminate\Support\Str::limit($mail->contents,150,' ...')}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </section>
                </aside>
                <div class="email-details dis-none h-100p bd-l-blue">
                    <section>
                        <div class="email-subject row">
                            <div class="col-sm-1">
                                <i class="fa-arrow-circle-o-left go-back-list fa-4x fa"></i>
                            </div>
                            <div class="col-sm-11">
                                <h1></h1>
                            </div>
                            <div class="clearfix col-sm-12">
                                <p>from &nbsp;&nbsp;<strong><span class="sender badge badge-primary"></span></strong> &bull;&nbsp; <span class="date"></span></p>
                                <div class="pos-rel pull-left">
                                    <button type="button" class="btn btn-danger btn-rounded btn-sm m-l-10 btnRemoveNotification" tartget="">Delete</button>
                                </div>
                            </div>
                        </div>
                        <div class="email-details-inner withScroll" data-height="window" data-padding="155">
                            <div class="email-content">
                            </div>
                        </div>
                    </section>
                </div>
            </section>
        </div>
    </div>
    <div class="clear-fix"></div>
@endfePortlet
@endsection