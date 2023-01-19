@extends('pages.master')
@section('css')
@endsection
@section('content')
<div class="row">
    <div class="col-sm-12 col-lg-12">
        <div class="card">
            <div class="card-header justify-content-between">
                <div class="header-title">
                    <div class="row">
                        <div class="col-sm-6 col-lg-6">
                            <h4 class="card-title">Data Dokter </h4>
                        </div>
                        <div class="col-sm-6 col-lg-6">
                            <a href="{{ route('dokter.create') }}" class="btn btn-primary float-end">
                                <i class="fa-solid fa-plus"></i> Tambah
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="dt" class="table table-bordered w-100">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Spesialis</th>
                                <th>Alamat</th>
                                <th>No HP</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script src="https://cdn.datatables.net/buttons/2.3.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.3.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.3.0/js/responsive.bootstrap.min.js"></script>
<script>
    $(document).ready(function() {
        $('#dt').DataTable({

            responsive: true,
            serverSide: true,
            processing: true,
            ajax: {
                url: `{{ route('dokter.index') }}`
            },
            columns: [{
                    data: 'nama',
                    name: 'nama'
                },
                {
                    data: 'spesialis',
                    name: 'spesialis'
                },
                {
                    data: 'alamat',
                    name: 'alamat'
                },
                {
                    data: 'no_tlp',
                    name: 'no_tlp'
                },
                {
                    data: 'action',
                    name: 'action',
                    className: "text-center",
                    orderable: false,
                    searchable: false
                },
            ],
            rowCallback: function(row, data) {
                let api = this.api();
                $(row).find('.btn-delete').click(function() {
                    let pk = $(this).data('id'),
                        url = `{{ route("dokter.index") }}/` + pk;
                    Swal.fire({
                        title: "Anda Yakin ?",
                        text: "Data tidak dapat dikembalikan setelah di hapus!",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Ya, Hapus!",
                        cancelButtonText: "Tidak, Batalkan",
                    }).then((result) => {
                        if (result.value) {
                            $.ajax({
                                url: url,
                                type: "DELETE",
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    _method: 'DELETE'
                                },
                                error: function(response) {
                                    toastr.error(response, 'Failed !');
                                },
                                success: function(response) {
                                    if (response.status === "success") {
                                        toastr.success(response.message, 'Success !');
                                        api.draw();
                                    } else {
                                        toastr.error((response.message ? response.message : "Please complete your form"), 'Failed !');
                                    }
                                }
                            });
                        }
                    });
                });
            }
        });
    });
</script>
@endsection