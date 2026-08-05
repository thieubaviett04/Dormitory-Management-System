<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Danh sách phòng</title>
</head>

<body>

    <h1>Danh sách phòng</h1>

    <a href="{{ route('rooms.create') }}">Thêm phòng</a>

    <br><br>

    <table border="1" cellpadding="8">
        <tr>
            <th>ID</th>
            <th>Tòa nhà</th>
            <th>Số phòng</th>
            <th>Tầng</th>
            <th>Sức chứa</th>
            <th>Trạng thái</th>
            <th>Hành động</th>
        </tr>

        @foreach($rooms as $room)
        <tr>
            <td>{{ $room->id }}</td>
            <td>{{ $room->building->name }}</td>
            <td>{{ $room->room_number }}</td>
            <td>{{ $room->floor }}</td>
            <td>{{ $room->capacity }}</td>
            <td>{{ $room->status }}</td>

            <td>
                <a href="{{ route('rooms.edit', $room->id) }}">Sửa</a>

                <form action="{{ route('rooms.destroy', $room->id) }}"
                    method="POST"
                    style="display:inline;">
                    @csrf
                    @method('DELETE')

                    <button type="submit"
                        onclick="return confirm('Bạn có chắc muốn xóa phòng này?')">
                        Xóa
                    </button>
                </form>
            </td>
        </tr>
        @endforeach

    </table>

</body>

</html>