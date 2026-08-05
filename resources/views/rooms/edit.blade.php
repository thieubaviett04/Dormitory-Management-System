<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Sửa phòng</title>
</head>

<body>

    <h1>Sửa phòng</h1>

    <form action="{{ route('rooms.update', $room->id) }}" method="POST">
        @csrf
        @method('PUT')

        <p>
            <label>Tòa nhà</label><br>
            <select name="building_id">
                @foreach($buildings as $building)
                <option value="{{ $building->id }}"
                    {{ $room->building_id == $building->id ? 'selected' : '' }}>
                    {{ $building->name }}
                </option>
                @endforeach
            </select>
        </p>

        <p>
            <label>Số phòng</label><br>
            <input type="text" name="room_number" value="{{ $room->room_number }}">
        </p>

        <p>
            <label>Tầng</label><br>
            <input type="number" name="floor" value="{{ $room->floor }}">
        </p>

        <p>
            <label>Sức chứa</label><br>
            <input type="number" name="capacity" value="{{ $room->capacity }}">
        </p>

        <p>
            <label>Trạng thái</label><br>
            <select name="status">
                <option value="available" {{ $room->status == 'available' ? 'selected' : '' }}>
                    Available
                </option>

                <option value="full" {{ $room->status == 'full' ? 'selected' : '' }}>
                    Full
                </option>

                <option value="maintenance" {{ $room->status == 'maintenance' ? 'selected' : '' }}>
                    Maintenance
                </option>
            </select>
        </p>

        <button type="submit">Cập nhật</button>

    </form>

    <br>

    <a href="{{ route('rooms.index') }}">Quay lại</a>

</body>

</html>