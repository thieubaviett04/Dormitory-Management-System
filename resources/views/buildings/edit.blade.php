<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Sửa tòa nhà</title>
</head>

<body>

    <h1>Sửa tòa nhà</h1>

    <form action="{{ route('buildings.update', $building->id) }}" method="POST">
        @csrf
        @method('PUT')

        <p>
            <label>Mã tòa nhà</label><br>
            <input type="text" name="code" value="{{ $building->code }}">
        </p>

        <p>
            <label>Tên tòa nhà</label><br>
            <input type="text" name="name" value="{{ $building->name }}">
        </p>

        <p>
            <label>Số tầng</label><br>
            <input type="number" name="floors" value="{{ $building->floors }}">
        </p>

        <p>
            <label>Mô tả</label><br>
            <textarea name="description">{{ $building->description }}</textarea>
        </p>

        <button type="submit">Cập nhật</button>
    </form>

    <br>

    <a href="{{ route('buildings.index') }}">Quay lại</a>

</body>

</html>