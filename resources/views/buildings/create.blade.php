<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Thêm tòa nhà</title>
</head>

<body>

    <h1>Thêm tòa nhà</h1>

    <form action="{{ route('buildings.store') }}" method="POST">
        @csrf

        <p>
            <label>Mã tòa nhà</label><br>
            <input type="text" name="code">
        </p>

        <p>
            <label>Tên tòa nhà</label><br>
            <input type="text" name="name">
        </p>

        <p>
            <label>Số tầng</label><br>
            <input type="number" name="floors">
        </p>

        <p>
            <label>Mô tả</label><br>
            <textarea name="description"></textarea>
        </p>

        <button type="submit">
            Lưu
        </button>

    </form>

    <br>

    <a href="{{ route('buildings.index') }}">
        Quay lại
    </a>

</body>

</html>