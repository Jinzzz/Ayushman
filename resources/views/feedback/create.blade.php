<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Feedback Form')}}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Titillium+Web:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('assets/general/style.css')}}">
    <link rel="stylesheet" href="{{asset('assets/general/responsive.css')}}">
</head>

<body>
    <div class="feedback-main">
        <div class="feedback-inner">
            <div class="container">
                <div class="logo-feedback">
                    <img src="{{asset('assets/images/ayushman-logo.jpeg')}}">
                </div>
                <div class="feedback-form">
                    <h3>
                        Feedback form
                    </h3>
                    <p>Thank you for choosing Ayushman. We appreciate your patronage. Please provide feedback on your visit so we can continue to improve our customer experience</p>
                </div>
                <div class="feedback">
                    <form action="{{ route('customer.feedback.save') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" value="{{$booking->id}}" name="booking_id">
                        <table width="100%" cellspacing="20">
                            <thead>
                                <tr cellpadding="20">
                                    <th width="10%"></th>
                                    <th width="10%">Very satisfied</th>
                                    <th width="10%">satisfied</th>
                                    <th width="10%">Neutral</th>
                                    <th width="10%">unsatisfied</th>
                                    <th width="10%">very unsatisfied</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="table-right">Consultant <br>Doctor</td>
                                    <td>
                                        <div class="bakground-checkbox">
                                            <label class="containerr">
                                                <input type="radio"  name="consultancy_rating" value="5">
                                                <span class="checkmark"></span>
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="bakground-checkbox"><label class="containerr">
                                                <input type="radio"  name="consultancy_rating" value="4">
                                                <span class="checkmark"></span>
                                            </label></div>
                                    </td>
                                    <td>
                                        <div class="bakground-checkbox"><label class="containerr">
                                                <input type="radio"  name="consultancy_rating" value="3">
                                                <span class="checkmark"></span>
                                            </label></div>
                                    </td>
                                    <td>
                                        <div class="bakground-checkbox"><label class="containerr">
                                                <input type="radio"  name="consultancy_rating" value="2">
                                                <span class="checkmark"></span>
                                            </label></div>
                                    </td>
                                    <td>
                                        <div class="bakground-checkbox"><label class="containerr">
                                                <input type="radio"  name="consultancy_rating" value="1">
                                                <span class="checkmark"></span>
                                            </label></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="table-right">The overall visit<br> experience</td>
                                    <td>
                                        <div class="bakground-checkbox">
                                            <label class="containerr">
                                                <input type="radio"  name="visit_rating" value="5">
                                                <span class="checkmark"></span>
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="bakground-checkbox"><label class="containerr">
                                                <input type="radio"  name="visit_rating" value="4">
                                                <span class="checkmark"></span>
                                            </label></div>
                                    </td>
                                    <td>
                                        <div class="bakground-checkbox"><label class="containerr">
                                                <input type="radio"  name="visit_rating" value="3">
                                                <span class="checkmark"></span>
                                            </label></div>
                                    </td>
                                    <td>
                                        <div class="bakground-checkbox"><label class="containerr">
                                                <input type="radio"  name="visit_rating" value="2">
                                                <span class="checkmark"></span>
                                            </label></div>
                                    </td>
                                    <td>
                                        <div class="bakground-checkbox"><label class="containerr">
                                                <input type="radio"  name="visit_rating" value="1">
                                                <span class="checkmark"></span>
                                            </label></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="table-right" style="width: 13%;">The service you received<br>from staff
                                        members</td>
                                    <td>
                                        <div class="bakground-checkbox">
                                            <label class="containerr">
                                                <input type="radio"  name="service_rating" value="5">
                                                <span class="checkmark"></span>
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="bakground-checkbox"><label class="containerr">
                                                <input type="radio"  name="service_rating" value="4">
                                                <span class="checkmark"></span>
                                            </label></div>
                                    </td>
                                    <td>
                                        <div class="bakground-checkbox"><label class="containerr">
                                                <input type="radio"  name="service_rating" value="3">
                                                <span class="checkmark"></span>
                                            </label></div>
                                    </td>
                                    <td>
                                        <div class="bakground-checkbox"><label class="containerr">
                                                <input type="radio"  name="service_rating" value="2">
                                                <span class="checkmark"></span>
                                            </label></div>
                                    </td>
                                    <td>
                                        <div class="bakground-checkbox"><label class="containerr">
                                                <input type="radio"  name="service_rating" value="1">
                                                <span class="checkmark"></span>
                                            </label></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="table-right">Pharmacy</td>
                                    <td>
                                        <div class="bakground-checkbox">
                                            <label class="containerr">
                                                <input type="radio"  name="pharmacy_rating" value="5">
                                                <span class="checkmark"></span>
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="bakground-checkbox"><label class="containerr">
                                                <input type="radio"  name="pharmacy_rating" value="4">
                                                <span class="checkmark"></span>
                                            </label></div>
                                    </td>
                                    <td>
                                        <div class="bakground-checkbox"><label class="containerr">
                                                <input type="radio"  name="pharmacy_rating" value="3">
                                                <span class="checkmark"></span>
                                            </label></div>
                                    </td>
                                    <td>
                                        <div class="bakground-checkbox"><label class="containerr">
                                                <input type="radio"  name="pharmacy_rating" value="2">
                                                <span class="checkmark"></span>
                                            </label></div>
                                    </td>
                                    <td>
                                        <div class="bakground-checkbox"><label class="containerr">
                                                <input type="radio"  name="pharmacy_rating" value="1">
                                                <span class="checkmark"></span>
                                            </label></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="table-right">Ease of getting an<br>appointment</td>
                                    <td>
                                        <div class="bakground-checkbox">
                                            <label class="containerr">
                                                <input type="radio"  name="appointment_rating" value="5">
                                                <span class="checkmark"></span>
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="bakground-checkbox"><label class="containerr">
                                                <input type="radio"  name="appointment_rating" value="4">
                                                <span class="checkmark"></span>
                                            </label></div>
                                    </td>
                                    <td>
                                        <div class="bakground-checkbox"><label class="containerr">
                                                <input type="radio"  name="appointment_rating" value="3">
                                                <span class="checkmark"></span>
                                            </label></div>
                                    </td>
                                    <td>
                                        <div class="bakground-checkbox"><label class="containerr">
                                                <input type="radio"  name="appointment_rating" value="2">
                                                <span class="checkmark"></span>
                                            </label></div>
                                    </td>
                                    <td>
                                        <div class="bakground-checkbox"><label class="containerr">
                                                <input type="radio"  name="appointment_rating" value="1">
                                                <span class="checkmark"></span>
                                            </label></div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="feedback-textarea">
                            <label>TELL US HOW WE CAN IMPROVE</label>
                            <textarea id="w3review" name="feedback"></textarea>
                        </div>
                        <!-- <div class="feedback-name">
                            <label>Full Name</label>
                            <input type="text">
                        </div> -->
                        <button class="submit-button">
                            Submit Your Feedback
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div> 
</body>

</html>