<div class="notification_main_sec nav-item dropdown realTimeNotificationDataUpdate">
                    <a class="nav-link" data-toggle="dropdown" href="#">
                        <i class="fa fa-bell"></i>

                        @if($notificaiton_count > 0)
                        <span class="notificaiton_count">{{$notificaiton_count}}</span>
                        @endif
                    </a>
                    <div class="dropdown-menu dropdown-menu-right notification_main">
                        <div class="notification_inner_scroll">
                            <div class="clear-all-cls">
                                <a href="javascript:void(0)" onclick="clearAllNotification()">{{__('backend.Clear_All')}}</a>
                            </div>

                            <?php if ($notificaiton_list && count($notificaiton_list)) {
                                foreach ($notificaiton_list as $key => $value) { ?>
                                    <div class="notification_wrap <?php echo ($value->is_read == 0) ? 'active' : ''; ?>">
                                        <?php
                                        if ($value->notification_for == 'create_challenge') {
                                            $redirectUrl = 'upcoming_booking';
                                        } elseif ($value->notification_for == 'book_court') {
                                            $redirectUrl = 'upcoming_booking';
                                        } elseif ($value->notification_for == 'booking_cancel') {
                                            $redirectUrl = 'cancelled_booking';
                                        } elseif ($value->notification_for == 'create_user') {
                                            $redirectUrl = 'my_account';
                                        } elseif ($value->notification_for == 'invite_player') {
                                            $redirectUrl = 'challenges_detail/' . $value->order_id;
                                        } elseif ($value->notification_for == 'accepted_challenge') {
                                            $redirectUrl = 'challenges_detail/' . $value->order_id;
                                        } elseif ($value->notification_for == 'join_challenge') {
                                            $redirectUrl = 'challenges_detail/' . $value->order_id;
                                        }
                                        elseif ($value->notification_for == 'post_payment_not_received') {
                                            $redirectUrl = 'cancelled_booking';
                                        }
                                         else {
                                            $redirectUrl = '';
                                        }
                                        ?>
                                        <p class=" m-b-0"><a href="javascript:void(0);" data-id="{{$value->id}}" data-url="{{url('').'/'.$redirectUrl}}" onclick="readNotification(this)"># {{$value->order_id}} {!!$value->message!!}</a></p>

                                    </div>
                                <?php }
                            } else { ?>
                                <div class="notification_wrap">
                                    <p class="m-b-0">{{__('backend.No_data_found')}}</p>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>