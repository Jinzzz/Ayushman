<!DOCTYPE html>
<html>
<head>
	<title>Ayushman Ayurveda | Invoice</title>
</head>
<body style="box-sizing: border-box; margin: 0; font-family: Arial, Helvetica, sans-serif;">

<div style="max-width: 600px; margin: auto;">
	<table style="width: 100%; border: none; border-top: 5px solid #3DAA33;">
	  <colgroup>
	  	<col width="10%">
	  	<col width="10%">
	  	<col width="10%">
	  	<col width="10%">
	  	<col width="10%">
	  	<col width="10%">
	  	<col width="10%">
	  	<col width="10%">
	  	<col width="10%">
	  	<col width="10%">
	  </colgroup>
	  <tr style="vertical-align: bottom; border-bottom: 1px solid #000;">
	    <th colspan="5" style="text-align: left; border: none; padding: 15px 0;">
	    	<img src="https://gcdnb.pbrd.co/images/UzY2RLjm3Sb1.png?o=1" style="max-width: 200px;">
	    </th>
	    <th colspan="5" style="text-align: right; border: none; padding: 15px 0;">
	    	<h2 style="margin: 0 0 13px;">Invoice</h2>
	    </th>
	  </tr>
	  <tr><td colspan="10" style="border-top: 1px solid #ddd;"></td></tr>
	  <tr>
	    <td colspan="5" style="border: none; padding: 15px 0 0;">
	    	<p style="margin: 0;">Invoice No.:
	    	<b>{{$data['sales_invoice_number']}}</b></p>
	    </td>
	    <td colspan="5" style="border: none; text-align: right; padding: 15px 0 0;">
	    	<p style="margin: 0;">Date:
	    	<b>{{ date('d-m-Y', strtotime($data['invoice_date'])) }}</b></p>
	    </td>
	  </tr>
	  <tr>
	    <td colspan="5" style="border: none; padding: 15px 0;">
	    	<p style="margin: 0;">Patient Name:
	    	<b>{{$data['patient_name']}}</b></p>
	    </td>
	    <td colspan="5" style="border: none; text-align: right; padding: 15px 0;">
	    	<p style="margin: 0;">Patient Code:
	    	<b>{{$data['patient_code']}}</b></p>
	    </td>
	  </tr>
	</table>

	<table style="width: 100%; border: 1px solid #999; border-collapse: collapse; margin: 10px 0 0;">
	  	<colgroup>
		  	<col width="10%">
		  	<col width="10%">
		  	<col width="10%">
		  	<col width="10%">
		  	<col width="10%">
		  	<col width="10%">
		  	<col width="10%">
		  	<col width="10%">
		  	<col width="10%">
		  	<col width="10%">
		</colgroup>
	  	<tr style="text-align: left;">
	  		<th colspan="6" style="padding: 10px; border: 1px solid #999; background: #f1f2f2;">Medicine Name</th>
	  		<th colspan="1" style="padding: 10px; border: 1px solid #999; background: #f1f2f2;">Qty</th>
	  		<th colspan="1" style="padding: 10px; border: 1px solid #999; background: #f1f2f2;">Amt</th>
	  		<th colspan="1" style="padding: 10px; border: 1px solid #999; background: #f1f2f2;">Mfd</th>
	  		<th colspan="1" style="padding: 10px; border: 1px solid #999; background: #f1f2f2;">Expd</th>
	  	</tr>
          @foreach($medicine_sale_details as $invoice)
	  	<tr>
	  		<td colspan="6" style="padding: 10px; border: 1px solid #999;">{{$invoice->medicine->medicine_name}}</td>
	  		<td colspan="1" style="padding: 10px; border: 1px solid #999;">{{intval($invoice->quantity)}}</td>
	  		<td colspan="1" style="padding: 10px; border: 1px solid #999;">{{ number_format($invoice->amount, 2) }}</td>
            <td colspan="1" style="padding: 10px; border: 1px solid #999;">{{ date('d-m-Y', strtotime($invoice->manufactured_date)) }}</td>
            <td colspan="1" style="padding: 10px; border: 1px solid #999;">{{ date('d-m-Y', strtotime($invoice->expiry_date)) }}</td>
	  	</tr>
          @endforeach
	</table>

	<table style="width: 100%; border: none;">
	  <colgroup>
	  	<col width="10%">
	  	<col width="10%">
	  	<col width="10%">
	  	<col width="10%">
	  	<col width="10%">
	  	<col width="10%">
	  	<col width="10%">
	  	<col width="10%">
	  	<col width="10%">
	  	<col width="10%">
	  </colgroup>
	  <tr>
	    <td colspan="10" style="border: none; padding: 10px 0 0; text-align: right;">
	    	<p style="margin: 15px 0 0;">Subtotal : {{$data['sub_total']}} /-</p>
	    	<p style="margin: 15px 0 0;">Tax Amount : {{$data['tax_amount']}} /-</p>
	    	<p style="margin: 15px 0 0;"><b>Total Amount : {{$data['total_amount']}} /-</b></p>
	    	<p style="margin: 15px 0 0;">Discount Amount : {{$data['discount_amount']}} /-</p>
            <h3 style="margin: 15px 0 0;"><b>Payable Amount : {{$data['payable_amount']}} /-</b></h3>
	    </td>
	  </tr>
	  <tr>
	    <td colspan="10" style="border: none; padding: 10px 0 0; text-align: center;">
	    	<p style="padding: 15px 0; background: #004f27; color: #fff;">Thank you for choosing our services. Your satisfaction is our priority.</p>
	    </td>
	  </tr>
	</table>

</div>

</body>
</html>