@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>SQL Query Executor</h1>

        <form id="sqlForm" method="POST" action="/dev/execute" class="mb-4">
            @csrf
            <div class="form-group">
                <textarea name="sql" id="sql" rows="4" class="form-control">{{ old('sql', $sql ?? '') }}</textarea>
            </div>

            <!-- 分页的隐藏字段，默认第一页 -->
            <input type="hidden" name="page" id="page" value="{{ request()->input('page', 1) }}">

            @if (!empty($error))
                <div class="alert alert-danger" style="margin-top: 20px;">{{ $error }}</div>
            @endif
            <div style="margin-top: 20px;">
                <button type="submit" class="btn btn-primary">Execute</button>
                <button type="button" class="btn btn-success" onclick="exportTo('excel')">Export to Excel</button>
                <button type="button" class="btn btn-info" onclick="exportTo('json')">Export to JSON</button>
            </div>
        </form>

        @if (!empty($results) && $results instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        @foreach(array_keys((array)$results[0]) as $key)
                            <th>{{ $key }}</th>
                        @endforeach
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($results as $row)
                        <tr>
                            @foreach ((array)$row as $value)
                                <td>{{ $value }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <!-- 分页链接 -->
            <div class="d-flex justify-content-center mt-4">
                @if ($results->currentPage() > 1)
                    <button onclick="submitPage({{ $results->currentPage() - 1 }})" class="btn btn-secondary">Previous</button>
                @endif

                @if ($results->currentPage() < $results->lastPage())
                    <button onclick="submitPage({{ $results->currentPage() + 1 }})" class="btn btn-secondary" style="margin-left: 20px;">Next</button>
                @endif
            </div>
        @endif
    </div>

    <script>
        function submitPage(page) {
            document.getElementById('page').value = page;
            document.getElementById('sqlForm').submit();
        }

        function exportTo(format) {
            const sqlQuery = document.getElementById('sql').value;
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = format === 'excel' ? '/dev/export/excel' : '/dev/export/json';
            form.innerHTML = `@csrf <input type="hidden" name="sql_query" value="${sqlQuery}">`;
            document.body.appendChild(form);
            form.submit();
        }
    </script>
@endsection
