<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Danh sách giường</title>
</head>

<body>

    <h1>Danh sách giường</h1>

    <a href="{{ route('beds.create') }}">Thêm giường</a>

    <br><br>

    <table border="1" cellpadding="8">
        <tr>
            <th>ID</th>
            <th>Tòa nhà</th>
            <th>Phòng</th>
            <th>Giường</th>
            <th>Trạng thái</th>
            <th>Hành động</th>
        </tr>

        @foreach($beds as $bed)
        <tr>
            <td>{{ $bed->id }}</td>
            <td>{{ $bed->room->building->name }}</td>
            <td>{{ $bed->room->room_number }}</td>
            <td>{{ $bed->bed_number }}</td>
            <td>{{ $bed->status }}</td>

            <td>
                <a href="{{ route('beds.edit', $bed->id) }}">Sửa</a>

                <form action="{{ route('beds.destroy', $bed->id) }}"
                    method="POST"
                    style="display:inline;">
                    @csrf
                    @method('DELETE')

                    <button type="submit"
                        onclick="return confirm('Bạn có chắc muốn xóa giường này?')">
                        Xóa
                    </button>
                </form>
            </td>
        </tr>
        @endforeach

    </table>

</body>

</html>