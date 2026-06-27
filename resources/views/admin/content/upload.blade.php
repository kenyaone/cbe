<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Content - CBE Platform</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f5f7fa; }
        .navbar { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; display: flex; justify-content: space-between; }
        .navbar a { color: white; text-decoration: none; margin-left: 20px; }
        .container { max-width: 800px; margin: 30px auto; padding: 0 20px; }
        .form-section { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 20px; }
        label { display: block; font-weight: 600; margin-bottom: 8px; color: #333; }
        input, select, textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 1em; }
        input:focus, select:focus { outline: none; border-color: #667eea; box-shadow: 0 0 5px rgba(102, 126, 234, 0.3); }
        .file-upload { padding: 20px; border: 2px dashed #667eea; border-radius: 4px; background: #f9f9f9; text-align: center; cursor: pointer; transition: all 0.3s; }
        .file-upload:hover { background: #f0f0f0; }
        .file-upload input[type="file"] { display: none; }
        .file-name { color: #667eea; margin-top: 10px; font-weight: 600; }
        .btn { padding: 10px 20px; border: none; border-radius: 4px; font-size: 1em; font-weight: 600; cursor: pointer; transition: all 0.3s; }
        .btn-primary { background: #667eea; color: white; }
        .btn-primary:hover { background: #5568d3; }
        .btn-primary:disabled { background: #ccc; cursor: not-allowed; }
        .error { color: #e74c3c; font-size: 0.9em; margin-top: 5px; }
        .success { color: #27ae60; font-size: 0.9em; margin-top: 5px; }
        .alert { padding: 15px; border-radius: 4px; margin-bottom: 20px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .section-title { font-size: 1.2em; font-weight: 600; margin-bottom: 20px; color: #333; }
        .two-column { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        @media (max-width: 768px) {
            .two-column { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>📤 Upload Content</h1>
        <div>
            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            <a href="{{ route('admin.content') }}">Content</a>
        </div>
    </div>

    <div class="container">
        @if ($errors->any())
            <div class="alert alert-error">
                <strong>Error!</strong>
                <ul style="margin-left: 20px; margin-top: 10px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="form-section">
            <h2 class="section-title">📝 Content Details</h2>
            <form action="{{ route('admin.content.upload.store') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                @csrf

                <div class="form-group">
                    <label for="title">Content Title *</label>
                    <input type="text" id="title" name="title" required placeholder="e.g., Introduction to Fractions" value="{{ old('title') }}">
                    @error('title')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <h2 class="section-title">📚 Curriculum Level</h2>
                <div class="two-column">
                    <div class="form-group">
                        <label for="grade">Grade Level *</label>
                        <select id="grade" name="grade" required>
                            <option value="">Select Grade Level</option>
                            @foreach($grades as $grade)
                                <option value="{{ $grade }}" {{ old('grade') == $grade ? 'selected' : '' }}>{{ $grade }}</option>
                            @endforeach
                        </select>
                        @error('grade')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="subject">Subject *</label>
                        <select id="subject" name="subject" required disabled>
                            <option value="">Select Subject</option>
                        </select>
                        @error('subject')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="two-column">
                    <div class="form-group">
                        <label for="strand">Strand (Topic) *</label>
                        <select id="strand" name="strand" required disabled>
                            <option value="">Select Strand</option>
                        </select>
                        @error('strand')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="sub_strand">Sub-Strand (Lesson) *</label>
                        <select id="sub_strand" name="sub_strand" required disabled>
                            <option value="">Select Sub-Strand</option>
                        </select>
                        @error('sub_strand')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <h2 class="section-title">📎 File Upload</h2>
                <div class="form-group">
                    <label for="file">Content File * (MP4, AVI, MOV, MKV, PDF, HTML)</label>
                    <div class="file-upload" onclick="document.getElementById('file').click()">
                        <div>📁 Click to select file</div>
                        <div style="font-size: 0.9em; color: #666; margin-top: 10px;">or drag and drop</div>
                        <input type="file" id="file" name="file" required accept=".mp4,.avi,.mov,.mkv,.flv,.wmv,.pdf,.html,.htm">
                        <div class="file-name" id="fileName"></div>
                    </div>
                    @error('file')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div style="display: flex; gap: 10px; margin-top: 30px;">
                    <button type="submit" class="btn btn-primary" id="submitBtn">Upload Content</button>
                    <a href="{{ route('admin.content') }}" class="btn" style="background: #6c757d; color: white; text-decoration: none;">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        const gradeSelect = document.getElementById('grade');
        const subjectSelect = document.getElementById('subject');
        const strandSelect = document.getElementById('strand');
        const subStrandSelect = document.getElementById('sub_strand');
        const fileInput = document.getElementById('file');
        const fileName = document.getElementById('fileName');
        const uploadForm = document.getElementById('uploadForm');

        // File upload display
        fileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                fileName.textContent = '✓ ' + this.files[0].name;
            } else {
                fileName.textContent = '';
            }
        });

        // Grade selection
        gradeSelect.addEventListener('change', async function() {
            const grade = this.value;
            subjectSelect.disabled = true;
            strandSelect.disabled = true;
            subStrandSelect.disabled = true;
            subjectSelect.innerHTML = '<option value="">Select Subject</option>';
            strandSelect.innerHTML = '<option value="">Select Strand</option>';
            subStrandSelect.innerHTML = '<option value="">Select Sub-Strand</option>';

            if (!grade) return;

            try {
                const response = await fetch(`/admin/content/subjects/${grade}`);
                const subjects = await response.json();

                subjects.forEach(subject => {
                    const option = document.createElement('option');
                    option.value = subject.id;
                    option.textContent = subject.name;
                    subjectSelect.appendChild(option);
                });
                subjectSelect.disabled = false;
            } catch (error) {
                console.error('Error loading subjects:', error);
            }
        });

        // Subject selection
        subjectSelect.addEventListener('change', async function() {
            const subjectId = this.value;
            strandSelect.disabled = true;
            subStrandSelect.disabled = true;
            strandSelect.innerHTML = '<option value="">Select Strand</option>';
            subStrandSelect.innerHTML = '<option value="">Select Sub-Strand</option>';

            if (!subjectId) return;

            try {
                const response = await fetch(`/admin/content/strands/${subjectId}`);
                const strands = await response.json();

                strands.forEach(strand => {
                    const option = document.createElement('option');
                    option.value = strand.id;
                    option.textContent = strand.name;
                    strandSelect.appendChild(option);
                });
                strandSelect.disabled = false;
            } catch (error) {
                console.error('Error loading strands:', error);
            }
        });

        // Strand selection
        strandSelect.addEventListener('change', async function() {
            const strandId = this.value;
            subStrandSelect.disabled = true;
            subStrandSelect.innerHTML = '<option value="">Select Sub-Strand</option>';

            if (!strandId) return;

            try {
                const response = await fetch(`/admin/content/sub-strands/${strandId}`);
                const subStrands = await response.json();

                subStrands.forEach(subStrand => {
                    const option = document.createElement('option');
                    option.value = subStrand.id;
                    option.textContent = subStrand.name;
                    subStrandSelect.appendChild(option);
                });
                subStrandSelect.disabled = false;
            } catch (error) {
                console.error('Error loading sub-strands:', error);
            }
        });

        // Restore form state if needed
        const oldGrade = '{{ old("grade") }}';
        if (oldGrade) {
            gradeSelect.value = oldGrade;
            gradeSelect.dispatchEvent(new Event('change'));
        }

        // Drag and drop
        const fileUpload = document.querySelector('.file-upload');
        fileUpload.addEventListener('dragover', (e) => {
            e.preventDefault();
            fileUpload.style.background = '#e8e8ff';
        });
        fileUpload.addEventListener('dragleave', () => {
            fileUpload.style.background = '#f9f9f9';
        });
        fileUpload.addEventListener('drop', (e) => {
            e.preventDefault();
            fileUpload.style.background = '#f9f9f9';
            if (e.dataTransfer.files.length > 0) {
                fileInput.files = e.dataTransfer.files;
                fileInput.dispatchEvent(new Event('change'));
            }
        });
    </script>
</body>
</html>
