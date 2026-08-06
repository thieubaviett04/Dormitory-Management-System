<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Thêm phòng</title>
</head>

<body>

    <h1>Thêm phòng</h1>

    <form action="{{ route('rooms.store') }}" method="POST">
        @csrf

        <p>
            <label>Tòa nhà</label><br>
            <select name="building_id">
                @foreach($buildings as $building)
                <option value="{{ $building->id }}">
                    {{ $building->name }}
                </option>
                @endforeach
            </select>
        </p>

        <p>
            <label>Số phòng</label><br>
            <input type="text" name="room_number">
        </p>

        <p>
            <label>Tầng</label><br>
            <input type="number" name="floor">
        </p>

        <p>
            <label>Sức chứa</label><br>
            <input type="number" name="capacity">
        </p>

        <p>
            <label>Trạng thái</label><br>
            <select name="status">
                <option value="available">Available</option>
                <option value="full">Full</option>
                <option value="maintenance">Maintenance</option>
            </select>
        </p>

        <button type="submit">Lưu</button>

    </form>

    <br>

    <a href="{{ route('rooms.index') }}">Quay lại</a>

</body>

</html>