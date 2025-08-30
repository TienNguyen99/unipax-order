<!DOCTYPE html>
<html>

<head>
    <title>Import Orders</title>
</head>

<body>
    <h1>Import Orders từ Excel</h1>

    @if (session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    <form action="{{ route('orders.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="file" required>
        <button type="submit">Import</button>
    </form>
</body>

</html>
