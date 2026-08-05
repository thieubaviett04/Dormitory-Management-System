<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách tòa nhà</title>
</head>

<body>

    <h1>Danh sách tòa nhà</h1>

    <a href="{{ route('buildings.create') }}">
        Thêm tòa nhà
    </a>

    <br><br>

    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Mã</th>
                <th>Tên</th>
                <th>Số tầng</th>
                <th>Mô tả</th>
                <th>Thao tác</th>
            </tr>
        </thead>

        <tbody>
            @forelse($buildings as $building)
            <tr>
                <td>{{ $building->id }}</td>
                <td>{{ $building->code }}</td>
                <td>{{ $building->name }}</td>
                <td>{{ $building->floors }}</td>
                <td>{{ $building->description }}</td>

                <td>
                    <a href="{{ route('buildings.edit', $building->id) }}">
                        Sửa
                    </a>

                    |

                    <form action="{{ route('buildings.destroy', $building->id) }}"
                        method="POST"
                        style="display:inline;">

                        @csrf
                        @method('DELETE')

                        <button type="submit">
                            Xóa
                        </button>

                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6">
                    Chưa có dữ liệu.
                </td>
            </tr>
            @endforelse
        </tbody>

    </table>

</body>

</html>