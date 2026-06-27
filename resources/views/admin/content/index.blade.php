<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Content - CBE Platform</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f5f7fa; }
        .navbar { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; display: flex; justify-content: space-between; }
        .navbar a { color: white; text-decoration: none; margin-left: 20px; }
        .container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }
        table { width: 100%; background: white; border-collapse: collapse; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        table th { background: #667eea; color: white; padding: 15px; text-align: left; }
        table td { padding: 15px; border-bottom: 1px solid #eee; }
        table tr:hover { background: #f9f9f9; }
        .badge { display: inline-block; padding: 4px 8px; border-radius: 4px; font-size: 0.85em; font-weight: 500; }
        .badge-video { background: #d4edda; color: #155724; }
        .badge-pdf { background: #cfe2ff; color: #084298; }
        .badge-interactive { background: #fff3cd; color: #664d03; }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>📁 Manage Content</h1>
        <div>
            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            <a href="{{ route('admin.users') }}">Admins</a>
        </div>
    </div>

    <div class="container">
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Type</th>
                    <th>File Path</th>
                    <th>Size</th>
                </tr>
            </thead>
            <tbody>
                @forelse($content as $file)
                    <tr>
                        <td>{{ $file->title }}</td>
                        <td>
                            <span class="badge badge-{{ strtolower($file->contentType->name) }}">
                                {{ $file->contentType->name }}
                            </span>
                        </td>
                        <td style="font-size: 0.9em; color: #666;">{{ basename($file->file_path) }}</td>
                        <td>
                            @php
                                if (file_exists($file->file_path)) {
                                    $size = filesize($file->file_path);
                                    $units = ['B', 'KB', 'MB', 'GB'];
                                    $bytes = $size;
                                    $unit = 0;
                                    while ($bytes >= 1024 && $unit < 3) {
                                        $bytes /= 1024;
                                        $unit++;
                                    }
                                    echo round($bytes, 2) . ' ' . $units[$unit];
                                }
                            @endphp
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: #999;">No content files found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{ $content->links() }}
    </div>
</body>
</html>
