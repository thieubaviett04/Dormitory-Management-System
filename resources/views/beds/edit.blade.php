<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Sửa giường</title>
</head>

<body>

    <h1>Sửa giường</h1>

    <form action="{{ route('beds.update', $bed->id) }}" method="POST">
        @csrf
        @method('PUT')

        <p>
            <label>Phòng</label><br>
            <select name="room_id">
                @foreach($rooms as $room)
                <option value="{{ $room->id }}"
                    {{ $bed->room_id == $room->id ? 'selected' : '' }}>
                    {{ $room->building->name }} - Phòng {{ $room->room_number }}
                </option>
                @endforeach
            </select>
        </p>

        <p>
            <label>Số giường</label><br>
            <input type="text" name="bed_number" value="{{ $bed->bed_number }}">
        </p>

        <p>
            <label>Trạng thái</label><br>
            <select name="status">
                <option value="available" {{ $bed->status == 'available' ? 'selected' : '' }}>
                    Available
                </option>

                <option value="occupied" {{ $bed->status == 'occupied' ? 'selected' : '' }}>
                    Occupied
                </option>

                <option value="maintenance" {{ $bed->status == 'maintenance' ? 'selected' : '' }}>
                    Maintenance
                </option>
            </select>
        </p>

        <button type="submit">Cập nhật</button>
    </form>

    <br>

    <a href="{{ route('beds.index') }}">Quay lại</a>

</body>

</html>