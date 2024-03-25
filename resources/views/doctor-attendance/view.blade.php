@extends('layouts.app') @section('content') <div style="max-width: none; padding: 0;" class="container">
  <div class="row" style="min-height: 70vh;">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h3 class="mb-0 card-title">Monthly Attendance View</h3>
        </div>
        <div class="col-lg-12" style="background-color:#fff;"> @if ($errors->any()) <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input. <br>
            <br>
            <ul> @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach </ul>
          </div> @endif <form action="{{ route('doctorattendance.monthly') }}" method="GET"> @csrf <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="form-label">Month and Year</label>
                  <input type="month" class="form-control" name="month_year" value="{{ $selectedMonthYear }}" />
                </div>
              </div>
              <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <div class="d-flex align-items-end">
                  <button type="submit" class="btn btn-primary">
                    <i class="fa fa-filter" aria-hidden="true"></i> Filter </button>&nbsp; <a class="btn btn-primary" href="{{ route('doctor.attendance.view') }}">
                    <i class="fa fa-times" aria-hidden="true"></i> Reset </a>
                </div>
              </div>
            </div>
          </form>
          </br>
          </br>
          <!-- Display the filtered table here -->
          <div class="table-responsive">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>Days</th>
                 @for ($day = 1; $day <= $daysInMonth; $day++) <th>{{ $day }}</th> @endfor
                </tr>
              </thead>
              <tbody>
                <style>
                  .present-cell {
                    color: #4CAF50;
                  }

                  .absent-cell {
                    color: #f44336;
                  } 

                  .empty-cell {
                    color: #CCCCCC;
                  }
                </style>
              @foreach ($allStaff as $staff)
    <tr>
        
     
        @for ($day = 1; $day <= $daysInMonth; $day++)
            @php
                $currentDate = $firstDayOfMonth->copy()->addDays($day - 1);
                $isDateBeforeJoin = $currentDate->lt($staff->date_of_join);
                $isFutureDate = $currentDate->isFuture();
                $leaveForDay = $staffLeaves->where('staff_id', $staff->user_id)
                    ->where('from_date', '<=', $currentDate->format('Y-m-d'))
                    ->where('to_date', '>=', $currentDate->format('Y-m-d'))
                    ->first();
            @endphp
            <td>
                @if ($isDateBeforeJoin || $isFutureDate)
                    <div class="empty-cell"></div>
                @elseif ($leaveForDay)
                    <div class="absent-cell">L</div>
                @else
                    <div class="present-cell">P</div>
                @endif
            </td>
        @endfor
    </tr>
@endforeach



              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div> @endsection @section('js') <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.ckeditor.com/4.17.2/standard/ckeditor.js"></script>
<script type="text/javascript">
  $(document).ready(function() {
    CKEDITOR.replace('medicalHistory', {
      removePlugins: 'image',
    });
    $(document).ready(function() {
      CKEDITOR.replace('currentMedication', {
        removePlugins: 'image',
      });
    });
  });

  function toggleStatus(checkbox) {
    if (checkbox.checked) {
      $("#statusText").text('Active');
      $("input[name=is_active]").val(1); // Set the value to 1 when checked
    } else {
      $("#statusText").text('Inactive');
      $("input[name=is_active]").val(0); // Set the value to 0 when unchecked
    }
  }
</script> @endsection