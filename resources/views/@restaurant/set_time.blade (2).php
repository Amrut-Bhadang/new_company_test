<div class="content">
<form method="POST" action="{{ url('restaurant/time_update/'.$restro_id) }}" id="time_role">
    @csrf    
<div class="row">
   <div class="col-12">
      <h5>Timings :</h5>
   </div>
   <div class="col-4">
      <div class="position-relative form-group">
         <label for="name" class="">Sunday Open Time</label>
         <div class="react-datepicker-wrapper">
            <div class="react-datepickerinput-container">
               <input type="text" class="datetimepicker form-control is_close_sunday sunday_open" name="day[sunday][open_time]" {{ ($time && $time['sunday']['is_close'] == 'Yes') ? 'disabled' : '' }} autocomplete="off" value="{{ ($time) ? $time['sunday']['start_time'] : '' }}">
               <input type="hidden" name="restro_id" id="restro_id" value="{{$restro_id}}">
            </div>
         </div>
      </div>
   </div>
   <div class="col-4">
      <div class="position-relative form-group">
         <label for="name" class="">Sunday Close Time</label>
         <div class="react-datepicker-wrapper">
            <div class="react-datepickerinput-container">
               <input type="text" class="datetimepicker form-control is_close_sunday sunday_close" name="day[sunday][close_time]" {{ ($time && $time['sunday']['is_close'] == 'Yes') ? 'disabled' : '' }} autocomplete="off" value="{{ ($time) ? $time['sunday']['end_time'] : '' }}">
            </div>
         </div>
      </div>
   </div>
   <div class="col-2">
      <div class="position-relative form-group">
         <label for="name" class="">Is Close On Sunday</label>
         <div>
            <div class="input-checkbox">
               <input type="checkbox" class="sunday_checkbox" onclick="disabledCheckbox('sunday')" name="day[sunday][is_close]" {{ ($time && $time['sunday']['is_close'] == 'Yes') ? 'checked' : '' }} value="Yes">Yes
            </div>
         </div>
      </div>
   </div>
   <div class="col-2">
      <div class="position-relative form-group">
         <button type="button" onclick="sameAllTime()" class="">Same All</button>
      </div>
   </div>
   <div class="col-4">
      <div class="position-relative form-group">
         <label for="name" class="">Monday Open Time</label>
         <div class="react-datepicker-wrapper">
            <div class="react-datepickerinput-container"><input type="text" class="datetimepicker form-control is_close_monday monday_open" autocomplete="off" name="day[monday][open_time]" {{ ($time && $time['monday']['is_close'] == 'Yes') ? 'disabled' : '' }} value="{{ ($time) ? $time['monday']['start_time'] : '' }}"></div>
         </div>
      </div>
   </div>
   <div class="col-4">
      <div class="position-relative form-group">
         <label for="name" class="">Monday Close Time</label>
         <div class="react-datepicker-wrapper">
            <div class="react-datepickerinput-container"><input type="text" class="datetimepicker form-control is_close_monday monday_close" autocomplete="off" name="day[monday][close_time]" {{ ($time && $time['monday']['is_close'] == 'Yes') ? 'disabled' : '' }} value="{{ ($time) ? $time['monday']['end_time'] : '' }}"></div>
         </div>
      </div>
   </div>
   <div class="col-4">
      <div class="position-relative form-group">
         <label for="name" class="">Is Close On Monday</label>
         <div>
            <div class="input-checkbox"><input type="checkbox" class="monday_checkbox" onclick="disabledCheckbox('monday')" name="day[monday][is_close]" {{ ($time && $time['monday']['is_close'] == 'Yes') ? 'checked' : '' }} value="Yes">Yes</div>
         </div>
      </div>
   </div>
   <div class="col-4">
      <div class="position-relative form-group">
         <label for="name" class="">Tuesday Open Time</label>
         <div class="react-datepicker-wrapper">
            <div class="react-datepickerinput-container"><input type="text" name="day[tuesday][open_time]" {{ ($time && $time['tuesday']['is_close'] == 'Yes') ? 'disabled' : '' }} autocomplete="off" class="datetimepicker form-control is_close_tuesday tuesday_open" value="{{ ($time) ? $time['tuesday']['start_time'] : '' }}"></div>
         </div>
      </div>
   </div>
   <div class="col-4">
      <div class="position-relative form-group">
         <label for="name" class="">Tuesday Close Time</label>
         <div class="react-datepicker-wrapper">
            <div class="react-datepickerinput-container"><input type="text" name="day[tuesday][close_time]" {{ ($time && $time['tuesday']['is_close'] == 'Yes') ? 'disabled' : '' }} autocomplete="off" class="datetimepicker form-control is_close_tuesday tuesday_close" value="{{ ($time) ? $time['tuesday']['end_time'] : '' }}"></div>
         </div>
      </div>
   </div>
   <div class="col-4">
      <div class="position-relative form-group">
         <label for="name" class="">Is Close On Tuesday</label>
         <div>
            <div class="input-checkbox"><input type="checkbox" class="tuesday_checkbox" onclick="disabledCheckbox('tuesday')" name="day[tuesday][is_close]" {{ ($time && $time['tuesday']['is_close'] == 'Yes') ? 'checked' : '' }} value="Yes">Yes</div>
         </div>
      </div>
   </div>
   <div class="col-4">
      <div class="position-relative form-group">
         <label for="name" class="">Wednesday Open Time</label>
         <div class="react-datepicker-wrapper">
            <div class="react-datepickerinput-container"><input type="text" name="day[wednesday][open_time]" {{ ($time && $time['wednesday']['is_close'] == 'Yes') ? 'disabled' : '' }} autocomplete="off" class="datetimepicker form-control is_close_wednesday wednesday_open" value="{{ ($time) ? $time['wednesday']['start_time'] : '' }}"></div>
         </div>
      </div>
   </div>
   <div class="col-4">
      <div class="position-relative form-group">
         <label for="name" class="">Wednesday Close Time</label>
         <div class="react-datepicker-wrapper">
            <div class="react-datepickerinput-container"><input type="text" name="day[wednesday][close_time]" {{ ($time && $time['wednesday']['is_close'] == 'Yes') ? 'disabled' : '' }} autocomplete="off" class="datetimepicker form-control is_close_wednesday wednesday_close" value="{{ ($time) ? $time['wednesday']['end_time'] : '' }}"></div>
         </div>
      </div>
   </div>
   <div class="col-4">
      <div class="position-relative form-group">
         <label for="name" class="">Is Close On Wednesday</label>
         <div>
            <div class="input-checkbox"><input type="checkbox" class="wednesday_checkbox" onclick="disabledCheckbox('wednesday')" name="day[wednesday][is_close]" {{ ($time && $time['wednesday']['is_close'] == 'Yes') ? 'checked' : '' }} value="Yes">Yes</div>
         </div>
      </div>
   </div>
   <div class="col-4">
      <div class="position-relative form-group">
         <label for="name" class="">Thursday Open Time</label>
         <div class="react-datepicker-wrapper">
            <div class="react-datepickerinput-container"><input type="text" name="day[thursday][open_time]" {{ ($time && $time['thursday']['is_close'] == 'Yes') ? 'disabled' : '' }} autocomplete="off" class="datetimepicker form-control is_close_thursday thursday_open" value="{{ ($time) ? $time['thursday']['start_time'] : '' }}"></div>
         </div>
      </div>
   </div>
   <div class="col-4">
      <div class="position-relative form-group">
         <label for="name" class="">Thursday Close Time</label>
         <div class="react-datepicker-wrapper">
            <div class="react-datepickerinput-container"><input type="text" name="day[thursday][close_time]" {{ ($time && $time['thursday']['is_close'] == 'Yes') ? 'disabled' : '' }} autocomplete="off" class="datetimepicker form-control is_close_thursday thursday_close" value="{{ ($time) ? $time['thursday']['end_time'] : '' }}"></div>
         </div>
      </div>
   </div>
   <div class="col-4">
      <div class="position-relative form-group">
         <label for="name" class="">Is Close On Thursday</label>
         <div>
            <div class="input-checkbox"><input type="checkbox" class="thursday_checkbox" onclick="disabledCheckbox('thursday')" name="day[thursday][is_close]" {{ ($time && $time['thursday']['is_close'] == 'Yes') ? 'checked' : '' }} value="Yes">Yes</div>
         </div>
      </div>
   </div>
   <div class="col-4">
      <div class="position-relative form-group">
         <label for="name" class="">Friday Open Time</label>
         <div class="react-datepicker-wrapper">
            <div class="react-datepickerinput-container"><input type="text" name="day[friday][open_time]" {{ ($time && $time['friday']['is_close'] == 'Yes') ? 'disabled' : '' }} autocomplete="off" class="datetimepicker form-control is_close_friday friday_open" value="{{ ($time) ? $time['friday']['start_time'] : '' }}"></div>
         </div>
      </div>
   </div>
   <div class="col-4">
      <div class="position-relative form-group">
         <label for="name" class="">Friday Close Time</label>
         <div class="react-datepicker-wrapper">
            <div class="react-datepickerinput-container"><input type="text" name="day[friday][close_time]" {{ ($time && $time['friday']['is_close'] == 'Yes') ? 'disabled' : '' }} autocomplete="off" class="datetimepicker form-control is_close_friday friday_close" value="{{ ($time) ? $time['friday']['end_time'] : '' }}"></div>
         </div>
      </div>
   </div>
   <div class="col-4">
      <div class="position-relative form-group">
         <label for="name" class="">Is Close On Friday</label>
         <div>
            <div class="input-checkbox"><input type="checkbox" class="friday_checkbox" onclick="disabledCheckbox('friday')" name="day[friday][is_close]" {{ ($time && $time['friday']['is_close'] == 'Yes') ? 'checked' : '' }} value="Yes">Yes</div>
         </div>
      </div>
   </div>
   <div class="col-4">
      <div class="position-relative form-group">
         <label for="name" class="">Saturday Open Time</label>
         <div class="react-datepicker-wrapper">
            <div class="react-datepickerinput-container"><input type="text" name="day[saturday][open_time]" {{ ($time && $time['saturday']['is_close'] == 'Yes') ? 'disabled' : '' }} autocomplete="off" class="datetimepicker form-control is_close_saturday saturday_open" value="{{ ($time) ? $time['saturday']['start_time'] : '' }}"></div>
         </div>
      </div>
   </div>
   <div class="col-4">
      <div class="position-relative form-group">
         <label for="name" class="">Saturday Close Time</label>
         <div class="react-datepicker-wrapper">
            <div class="react-datepickerinput-container"><input type="text" name="day[saturday][close_time]" {{ ($time && $time['saturday']['is_close'] == 'Yes') ? 'disabled' : '' }} autocomplete="off" class="datetimepicker form-control is_close_saturday saturday_close" value="{{ ($time) ? $time['saturday']['end_time'] : '' }}"></div>
         </div>
      </div>
   </div>
   <div class="col-4">
      <div class="position-relative form-group">
         <label for="name" class="">Is Close On Saturday</label>
         <div>
            <div class="input-checkbox"><input type="checkbox" class="saturday_checkbox" onclick="disabledCheckbox('saturday')" name="day[saturday][is_close]" {{ ($time && $time['saturday']['is_close'] == 'Yes') ? 'checked' : '' }} value="Yes">Yes</div>
         </div>
      </div>
   </div>
</div>
<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    <button type="submit" class="btn btn-primary float-right save"><span class="spinner-grow spinner-grow-sm formloader"
            style="display: none;" role="status" aria-hidden="true"></span> Save</button>
</form>
</div>

<script type="text/javascript">

   function sameAllTime() {
      var sunday_open = $('.sunday_open').val();
      var sunday_close = $('.sunday_close').val();
      var sunday_checkbox = $('.sunday_checkbox:checkbox:checked').val();
      var isClose = false;
      var is_disabled = '';

      if (sunday_checkbox == 'Yes') {
         isClose = true;
         is_disabled = 'disabled';
      }

      $('.sunday_open').prop("disabled", isClose);
      $('.sunday_close').prop("disabled", isClose);
      
      $('.monday_open').val(sunday_open).prop("disabled", isClose);
      $('.monday_close').val(sunday_close).prop("disabled", isClose);
      $(".monday_checkbox").prop( "checked", isClose );

      $('.tuesday_open').val(sunday_open).prop("disabled", isClose);
      $('.tuesday_close').val(sunday_close).prop("disabled", isClose);
      $(".tuesday_checkbox").prop( "checked", isClose );

      $('.wednesday_open').val(sunday_open).prop("disabled", isClose);
      $('.wednesday_close').val(sunday_close).prop("disabled", isClose);
      $(".wednesday_checkbox").prop( "checked", isClose );

      $('.thursday_open').val(sunday_open).prop("disabled", isClose);
      $('.thursday_close').val(sunday_close).prop("disabled", isClose);
      $(".thursday_checkbox").prop( "checked", isClose );

      $('.friday_open').val(sunday_open).prop("disabled", isClose);
      $('.friday_close').val(sunday_close).prop("disabled", isClose);
      $(".friday_checkbox").prop( "checked", isClose );

      $('.saturday_open').val(sunday_open).prop("disabled", isClose);
      $('.saturday_close').val(sunday_close).prop("disabled", isClose);
      $(".saturday_checkbox").prop( "checked", isClose );
   }

   function disabledCheckbox(day) {
      var checkbox = $('.'+day+'_checkbox:checkbox:checked').val();
      var isClose = false;
      var is_disabled = '';

      if (checkbox == 'Yes') {
         isClose = true;
         is_disabled = 'disabled';
      }

      $('.'+day+'_open').prop("disabled", isClose);
      $('.'+day+'_close').prop("disabled", isClose);
   }
   $(function () {
      $('.datetimepicker').datetimepicker({
          // Formats
          // follow MomentJS docs: https://momentjs.com/docs/#/displaying/format/
          format: 'HH:mm',

          // Your Icons
          // as Bootstrap 4 is not using Glyphicons anymore
          icons: {
              time: 'fa fa-clock-o',
              date: 'fa fa-calendar',
              up: 'fa fa-chevron-up',
              down: 'fa fa-chevron-down',
              previous: 'fa fa-chevron-left',
              next: 'fa fa-chevron-right',
              today: 'fa fa-check',
              clear: 'fa fa-trash',
              close: 'fa fa-times'
          }
      });
  });
</script>