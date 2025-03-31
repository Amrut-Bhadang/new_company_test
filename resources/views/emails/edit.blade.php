<?php

use App\Models\Language;
use App\Models\EmailTemplate;
use App\Models\EmailTemplateLang;

$language = Language::pluck('lang')->toArray();
?>
<form method="PUT" action="{{ url('admin/api/emails/'.$emails->id) }}" id="edit_role">
  @csrf
  <?php
  if (isset($emails)) {
    $emailData = EmailTemplate::where(['id' => $emails->id])->first();
  } ?>

  <ul class="nav nav-tabs">
    @foreach($language as $key => $lang)
    <li class="nav-item @if($key==0)active @endif"><a data-toggle="tab" href="#edittab{{$key}}" class="nav-link @if($key==0)active @endif">{{ __('backend.'.$lang)}}</a></li>
    @endforeach
  </ul>
  <div class="tab-content" style="margin-top:10px">
    @if($lang)
    @foreach($language as $key => $lang)
    <?php
    if (isset($emails)) {
      $langData = EmailTemplateLang::where(['lang' => $lang, 'email_id' => $emails->id])->first();
      // dd($langData); 
    } ?>
    <div id="edittab{{$key}}" class="tab-pane fade   @if($key==0) in active show @endif">
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label class="control-label" for="name"> {{__('backend.title')}} ({{__('backend.'.$lang)}}) *</label>
            <input type="text" name="name[{{$lang}}]" @if($key==0)data-parsley-required="true" @endif value="{{ $langData->name ?? '' }}" id="name" class="form-control" placeholder="{{__('backend.title')}}" />
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label class="control-label" for="subject"> {{__('backend.subject')}} ({{__('backend.'.$lang)}}) *</label>
            <input type="text" name="subject[{{$lang}}]" @if($key==0)data-parsley-required="true" @endif value="{{ $langData->subject ?? '' }}" id="subject" class="form-control" placeholder="{{__('backend.subject')}}" />
          </div>
        </div>

        <div class="col-md-12">
          <div class="form-group">
            <label class="control-label" for="description">{{__('backend.description')}} ({{__('backend.'.$lang)}}) *</label>
            <textarea class="form-control ckeditor" id="descriptionedit_{{$lang}}" @if($key==0)data-parsley-required="true" @endif name="description[{{$lang}}]" placeholder="{{__('backend.description')}}">{{$langData->description ?? ''}}</textarea>
          </div>
        </div>

        <div class="col-md-12">
          <div class="form-group">
            <label class="control-label" for="footer">{{__('backend.footer')}} ({{__('backend.'.$lang)}}) *</label>
            <input type="text" name="footer[{{$lang}}]" @if($key==0)data-parsley-required="true" @endif value="{{ $langData->footer ?? '' }}" id="footer" class="form-control" placeholder="{{__('backend.footer')}}" />
          </div>
        </div>
      </div>
    </div>
    @endforeach
    @endif
  </div>


  <hr style="margin: 1em -15px">
  <button type="button" class="btn btn-default" data-dismiss="modal">{{__('backend.Close')}}</button>
  <button type="submit" class="btn btn-primary float-right save"><span class="spinner-grow spinner-grow-sm formloader" style="display: none;" role="status" aria-hidden="true"></span> {{__('backend.Save')}}</button>

</form>
<script src="https://cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>

<script>
  $(document).ready(function() {
    $('.select2').select2();
    $('#edit_role').parsley();
    $("#edit_role").on('submit', function(e) {
      e.preventDefault();
      var _this = $(this);
      $('#group_loader').fadeIn();
      // var values = $('#edit_role').serialize();
      var descriptionedit_en = CKEDITOR.instances.descriptionedit_en.getData();
      var descriptionedit_ar = CKEDITOR.instances.descriptionedit_ar.getData();
      
      var formData = new FormData(this);
      formData.append('_method', 'put');
      formData.append('description[en]', descriptionedit_en);
      formData.append('description[ar]', descriptionedit_ar);
      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });
      $.ajax({
        url: "{{ url('admin/api/emails/'.$emails->id) }}",
        dataType: 'json',
        data: formData,
        type: 'POST',
        cache:false,
        contentType: false,
        processData: false,
        beforeSend: function() {
          before(_this)
        },
        // hides the loader after completion of request, whether successfull or failor.
        complete: function() {
          complete(_this)
        },
        success: function(result) {
          toastr.success(`User ${result.name} has been Updated!`)
          setTimeout(function() {
            $('#disappear_add').fadeOut('slow')
          }, 3000)
          $('#edit_role').parsley().reset();
          ajax_datatable.draw();
          location.reload();
        },
        error: function(jqXHR, textStatus, textStatus) {
          if (jqXHR.responseJSON.errors) {
            $.each(jqXHR.responseJSON.errors, function(index, value) {
              toastr.error(value)
            });
          } else {
            toastr.error(jqXHR.responseJSON.message)
          }
        }
      });
      return false;
    });
  });
</script>