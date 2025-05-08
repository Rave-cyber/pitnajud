<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order Tracking - FreshFold Laundry Service</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            color: #079CD6;
        }
        .section {
            margin-bottom: 25px;
        }
        .label {
            font-weight: bold;
        }
        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            color: white;
            background-color: #17a2b8;
            font-size: 0.9rem;
        }
        /* Status Timeline */
        .status-timeline {
            display: flex;
            justify-content: space-between;
            margin: 30px 0;
            position: relative;
        }
        .status-timeline::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 4px;
            background: #e0e0e0;
            z-index: 1;
        }
        .status-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            z-index: 2;
            width: 16%;
        }
        .status-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 8px;
            color: white;
        }
        .status-icon.active {
            background: #4CAF50;
        }
        .status-label {
            font-size: 12px;
            text-align: center;
            color: #757575;
        }
        .status-label.active {
            color: #4CAF50;
            font-weight: bold;
        }
        .status-timestamp {
            font-size: 10px;
            color: #888;
            margin-top: 5px;
            text-align: center;
            min-height: 15px;
        }
        .status-timestamp.active {
            color: #4CAF50;
            font-weight: bold;
        }
        .progress-bar {
            position: absolute;
            top: 20px;
            left: 0;
            height: 4px;
            background: #4CAF50;
            z-index: 2;
            transition: width 0.3s ease;
        }
        .btn-primary {
            background: #079CD6;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s;
            margin-top: 20px;
        }
        .btn-primary:hover {
            background: #067ba8;
        }
        footer {
            text-align: center;
            margin-top: 30px;
            color: #666;
            font-size: 14px;
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        
        .button-container a {
            margin: 0 10px;
        }
        
        .status-timestamp.inactive {
            color: #aaa;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Order Tracking</h1>

        <div class="section">
            <p><span class="label">Order Name:</span> {{ $order->order_name }}</p>
            <p><span class="label">Weight:</span> {{ $order->weight }} kg</p>
            <p><span class="label">Service Type:</span> {{ $order->service_type }}</p>
            <p><span class="label">Payment Method:</span> {{ $order->payment_method }}</p>
            <p><span class="label">Amount:</span> ${{ number_format($order->amount, 2) }}</p>
            <p><span class="label">Order Date:</span> {{ \Carbon\Carbon::parse($order->created_at)->format('M d, Y h:i A') }}</p>
            <p><span class="label">Current Status:</span> <span class="badge">{{ ucfirst($order->status) }}</span></p>
        </div>

        <div class="status-timeline">
        <div class="progress-bar" style="width: 
            @if($order->status == 'Pending') 0%
            @elseif($order->status == 'Washing') 20%
            @elseif($order->status == 'Drying') 40%
            @elseif($order->status == 'Ironing') 60%
            @elseif($order->status == 'Ready') 80%
            @elseif($order->status == 'Completed') 100%
            @endif">
        </div>
        
        <!-- Received - Always shows creation time -->
        <div class="status-step">
            <div class="status-icon active">
                <i class="fas fa-check"></i>
            </div>
            <span class="status-label active">Received</span>
            <div class="status-timestamp active">
                {{ \Carbon\Carbon::parse($order->created_at)->format('h:i A') }}
            </div>
        </div>
        
        <div class="status-step">
            <div class="status-icon @if(in_array($order->status, ['Washing', 'Drying', 'Ironing', 'Ready', 'Completed'])) active @endif">
                <i class="fas fa-soap"></i>
            </div>
            <span class="status-label @if(in_array($order->status, ['Washing', 'Drying', 'Ironing', 'Ready', 'Completed'])) active @endif">Washing</span>
            <div class="status-timestamp @if(in_array($order->status, ['Washing', 'Drying', 'Ironing', 'Ready', 'Completed'])) active @endif">
                @if($order->status == 'Washing' || $order->washing_started_at)
                    {{ \Carbon\Carbon::parse($order->washing_started_at ?? now())->format('h:i A') }}
                @endif
            </div>
        </div>
        
        <div class="status-step">
            <div class="status-icon @if(in_array($order->status, ['Drying', 'Ironing', 'Ready', 'Completed'])) active @endif">
                <i class="fas fa-wind"></i>
            </div>
            <span class="status-label @if(in_array($order->status, ['Drying', 'Ironing', 'Ready', 'Completed'])) active @endif">Drying</span>
            <div class="status-timestamp @if(in_array($order->status, ['Drying', 'Ironing', 'Ready', 'Completed'])) active @endif">
                @if($order->status == 'Drying' || $order->drying_started_at)
                    {{ \Carbon\Carbon::parse($order->drying_started_at ?? now())->format('h:i A') }}
                @endif
            </div>
        </div>
        
        <div class="status-step">
            <div class="status-icon @if(in_array($order->status, ['Ironing', 'Ready', 'Completed'])) active @endif">
                <i class="fas fa-tshirt"></i>
            </div>
            <span class="status-label @if(in_array($order->status, ['Ironing', 'Ready', 'Completed'])) active @endif">Ironing</span>
            <div class="status-timestamp @if(in_array($order->status, ['Ironing', 'Ready', 'Completed'])) active @endif">
                @if($order->status == 'Ironing' || $order->ironing_started_at)
                    {{ \Carbon\Carbon::parse($order->ironing_started_at ?? now())->format('h:i A') }}
                @endif
            </div>
        </div>
        
        <div class="status-step">
            <div class="status-icon @if(in_array($order->status, ['Ready', 'Completed'])) active @endif">
                <i class="fas fa-check-double"></i>
            </div>
            <span class="status-label @if(in_array($order->status, ['Ready', 'Completed'])) active @endif">Ready</span>
            <div class="status-timestamp @if(in_array($order->status, ['Ready', 'Completed'])) active @endif">
                @if($order->status == 'Ready' || $order->ready_at)
                    {{ \Carbon\Carbon::parse($order->ready_at ?? now())->format('h:i A') }}
                @endif
            </div>
        </div>
        
        <div class="status-step">
            <div class="status-icon @if($order->status == 'Completed') active @endif">
                <i class="fas fa-check-circle"></i>
            </div>
            <span class="status-label @if($order->status == 'Completed') active @endif">Completed</span>
            <div class="status-timestamp @if($order->status == 'Completed') active @endif">
                @if($order->status == 'Completed')
                    {{ \Carbon\Carbon::parse($order->completed_at ?? now())->format('h:i A') }}
                @endif
            </div>
        </div>
        <div class="button-container">
            <a href="{{ route('welcome') }}" class="btn-primary">
                <i class="fas fa-home"></i> Back to Dashboard
            </a>
        </div>
    </div>
    </div>

    <script>
    function updateOrderStatus(selectElement) {
        const orderId = selectElement.dataset.orderId;
        const newStatus = selectElement.value;
        
        fetch(`/orders/${orderId}/update-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                status: newStatus,
                timestamp: new Date().toISOString()
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
    </script>
</body>
</html>