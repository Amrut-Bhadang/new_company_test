<li class="nav-item dropdown opendrop2 realTimeNotificationDataUpdate">
                <a class="nav-link" data-toggle="dropdown" href="javascript:void(0)">
                <i class="fa fa-bell"></i>
                <span class="notificaiton_count">{{$notificaiton_count}}</span>
                </a>
                <div class="dropdown-menu dropdown-menu-right notification_main flipInY2">
                    <div class="notification_inner_scroll ">
                      <div class="clear-all-cls">
                          <a href="javascript:void(0)" onclick="clearAllNotification()">{{__('backend.Clear_All')}}</a>
                      </div>

                        <?php if ($notificaiton_list && count($notificaiton_list)) { foreach ($notificaiton_list as $key => $value) { ?>
                            <div class="notification_wrap <?php echo ($value->is_read == 0) ? 'active' : ''; ?>">
                                <?php
                                    if($value->notification_for == 'create_challenge'){
                                        $redirectUrl = 'orders';
                                    }
                                    elseif($value->notification_for == 'book_court'){
                                        $redirectUrl = 'orders';
                                    }
                                    elseif($value->notification_for == 'booking_cancel'){
                                        $redirectUrl = 'orders';
                                    }
                                    elseif($value->notification_for == 'create_user'){
                                        $redirectUrl = 'players';
                                    }
                                    else{
                                        $redirectUrl = '';
                                    }
                                    
                                ?>
                                <p class=" m-b-0"><a href="javascript:void(0);" data-id="{{$value->id}}" data-url="{{url('').'/admin/'.$redirectUrl}}" onclick="readNotification(this)"># {{$value->order_id}} {!!$value->message!!}</a></p>
                                <!-- <a href="#"><i class="fa fa-trash"></i></a> -->
                            </div>
                        <?php } } else { ?>
                            <div class="notification_wrap">
                                <p class="m-b-0">{{__('backend.No_data_found')}}</p>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </li>