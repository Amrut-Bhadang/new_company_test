<?php if ($notificationData && !empty($notificationData)) { foreach ($notificationData as $key => $value) { ?>
    <div class="notification_wrap <?php echo ($value->is_read == 0) ? 'active' : ''; ?>">
        <?php
            $redirectUrl = 'faq_request';
        ?>
        <p class=" m-b-0"><a href="{{url('admin/').'/'.$redirectUrl}}">{!!$value->message!!}</a></p>
        <!-- <a href="#"><i class="fa fa-trash"></i></a> -->
    </div>
<?php } } else { ?>
    <div class="notification_wrap">
        <p class="m-b-0">No data found!</p>
    </div>
<?php } ?>