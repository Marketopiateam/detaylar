@extends('admin.layouts.app')

@section('content')
    <div class="header bg-primary pb-6">
        <div class="container-fluid">
            <div class="header-body">
                <div class="row align-items-center py-4">
                    <div class="col-lg-6 col-7">
                        <h6 class="h2 text-black d-inline-block mb-0">Category</h6>
                        <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                            <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i
                                            class="fas fa-home"></i></a></li>
                                <li class="breadcrumb-item"><a href="{{ route('category') }}">Category</a></li>
                                <li class="breadcrumb-item">List</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="col-lg-6 col-5 text-right">
                        <a href="{{ route('category.add') }}" class="btn btn-lg btn-neutral fade-class"><i
                                class="fas fa-plus fa-lg"></i> New</a>
                        {{--                        <a href="#" class="btn btn-sm btn-neutral">Filters</a> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="container-fluid mt--6 mb--1">
        <div class="row">
            <div class="col">
                <div class="card">
                    <!-- Card header -->
                    <div class="card-header border-0">
                        <h3 class="mb-3">Filter</h3>
                        <form action="{{ route('category') }}">
                            <div class="row">
                                <div class="col-md-4 form-group">
                                    <input type="text" name="name" id="name" value="{{ request()->name }}"
                                        class="form-control form-control-alternative" placeholder="Search..." autofocus>
                                </div>
                                <div class="col-md-3 form-group">
                                    <button type="submit" class="btn btn-success"><i class="fas fa-search"></i></button>
                                    <a href="{{ route('category') }}" class="btn btn-info"><i
                                            class="fas fa-sync-alt"></i></a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Page content -->
    <div class="container-fluid ">
        <div class="row">
            <div class="col">
                <div class="card">
                    <!-- Card header -->

                    <!-- Light table -->
                    <div class="table-responsive">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-dark">
                                <tr>
                                    <th scope="col" class="sort" data-sort="name">Image</th>
                                    <th scope="col" class="sort" data-sort="name">Name</th>
                                    <th scope="col" class="sort" data-sort="name">Main Category</th>
                                    <th scope="col" class="sort" data-sort="parent_id">Parent</th>
                                    <th scope="col" class="sort" data-sort="status">Meta Title</th>
                                    <th scope="col" class="sort" data-sort="status">Status</th>
                                    <th scope="col" class="sort">Action</th>
                                </tr>
                            </thead>
                            <tbody class="list">

                                @forelse($records as $key => $value)
                                    <tr>
                                        <td class="budget">
                                            @if ($value->image)
                                                <img src="{{ asset("$value->image") }}"
                                                    alt="{{ $value->admincategoryDescription->name }}"
                                                    class="img-thumbnail img-fluid" style=" width: 60px;height: 60px;">
                                            @else
                                                <img src="{{ asset('/assets/img/default.png') }}" alt="{{ $value->name }}"
                                                    class="img-thumbnail img-fluid" style=" width: 60px;height: 60px;">
                                            @endif
                                        </td>
                                        <td class="budget">{{ $value->admincategoryDescription->name }}</td>
                                        @if ($value->men != false && $value->women != false)
                                            <td class="budget">
                                                Both Men and Women
                                            </td>
                                        @elseif ($value->men != false)
                                            <td class="budget">
                                                Men
                                            </td>
                                        @elseif ($value->women != false)
                                            <td class="budget">
                                                Women
                                            </td>
                                        @else
                                            <td class="budget">
                                                Other
                                            </td>
                                        @endif

                                        <td class="budget">{{ $value->parent_id == 0 ? 'Yes' : 'No' }}</td>
                                        <td class="budget">{{ $value->admincategoryDescription->meta_title }}</td>
                                        <td class="budget"><span
                                                class="p-2  @if ($value->status == 1) badge bg-success text-white  @else  badge bg-danger text-white @endif">{{ config('constant.status')[$value->status] }}
                                            </span></td>

                                        <td class="">
                                            <div class="dropdown">
                                                <a class="btn btn-sm btn-icon-only text-dark" href="#" role="button"
                                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                    <a class="dropdown-item"
                                                        href="{{ route('category.edit', ['id' => $value->category_id]) }}">Edit</a>
                                                    <a class="dropdown-item deleteData" type="button"
                                                        href="javascript:void(0)"
                                                        data-url="{{ route('category.delete', ['id' => $value->category_id]) }}">Delete</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="budget">
                                            No Record Found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <!-- Card footer -->
                    <div class="card-footer py-4">
                        {{ $records->appends(['name' => request()->name])->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).on('click', '.deleteData', function() {
            let alertMessage = "Are You Sure,Child Category will be delete If it Parent"
            let routeUrl = $(this).data('url')
            deleteData(alertMessage, routeUrl)
        })
    </script>
@endpush
