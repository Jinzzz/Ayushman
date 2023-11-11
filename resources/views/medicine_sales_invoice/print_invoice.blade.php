<!DOCTYPE html>
<html>
<head>
    <title>Invoice</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .invoice {
            width: 80%;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .invoice-details {
            display: flex;
            justify-content: space-between;
        }
        .invoice-info {
            font-size: 16px;
        }
        .invoice-items {
            margin-top: 20px;
            border-collapse: collapse;
            width: 100%;
        }
        .invoice-items th, .invoice-items td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="invoice">
        <div class="header">
            <h1>Invoice</h1>
        </div>

        <div class="invoice-details">
            <div class="invoice-info">
                <p>Invoice Number: {{$data['sales_invoice_number']}}</p>
                <p>Date: {{ date('d-m-y', strtotime($data['invoice_date'])) }}</p>
                <!-- Add more invoice details here -->
            </div>
            <div class="invoice-info">
                <p>Patient Name: {{$data['patient_name']}}</p>
                <p>Patient Code: {{$data['patient_code']}}</p>
                <!-- Add more customer details here -->
            </div>
        </div>

        <table class="invoice-items">
            <thead>
                <tr>
                    <th>Medicine Name</th>
                    <th>Quantity</th>
                    <th>Amount</th>
                    <th>Manufactured Date</th>
                    <th>Expiry Date</th>
                </tr>
            </thead>
            <tbody>
            @foreach($medicine_sale_details as $invoice)
                <tr>
                    <td>{{$invoice->medicine->medicine_name}}</td>
                    <td>{{$invoice->quantity}}</td>
                    <td>{{$invoice->amount}}</td>
                    <td>{{ date('d-m-y', strtotime($invoice->manufactured_date)) }}</td>
                    <td>{{ date('d-m-y', strtotime($invoice->expiry_date)) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="invoice-summary">
            <p>Subtotal : {{$data['sub_total']}} /-</p>
            <p>Tax Amount : {{$data['tax_amount']}} /-</p>
            <p>Total Amount : {{$data['total_amount']}} /-</p>
            <p>Discount Amount : {{$data['discount_amount']}} /-</p>
            <p>Payable Amount : {{$data['payable_amount']}} /-</p>
        </div>

        <div class="footer">
        <p>Thank you for choosing our services. Your satisfaction is our priority.</p>

        </div>
    </div>
</body>
</html>
