<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Thêm giường</title>
</head>

<body>

    <h1>Thêm giường</h1>

    <form action="{{ route('beds.store') }}" method="POST">
        @csrf

        <p>
            <label>Phòng</label><br>
            <select name="room_id">
                @foreach($rooms as $room)
                <option value="{{ $room->id }}">
                    {{ $room->building->name }} - Phòng {{ $room->room_number }}
                </option>
                @endforeach
            </select>
        </p>

        <p>
            <label>Số giường</label><br>
            <input type="text" name="bed_number">
        </p>

        <p>
            <label>Trạng thái</label><br>
            <select name="status">
                <option value="available">Available</option>
                <option value="occupied">Occupied</option>
                <option value="maintenance">Maintenance</option>
            </select>
        </p>

        <button type="submit">Lưu</button>

    </form>

    <br>

    <a href="{{ route('beds.index') }}">Quay lại</a>

</body>

</html>