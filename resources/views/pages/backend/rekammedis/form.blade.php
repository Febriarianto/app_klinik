@extends('pages.master')
@section('css')
@endsection
@section('content')
<form id="formStore" action="{{ $config['form']->action }}" method="POST">
    @method($config['form']->method)
    @csrf
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header justify-content-between">
                    <div class="header-title">
                        <div class="row">
                            <div class="col-sm-6 col-lg-6">
                                <h4 class="card-title">{{ $config['title'] }}</h4>
                            </div>
                            <div class="col-sm-6 col-lg-6">
                                <div class="btn-group float-end" role="group" aria-label="Basic outlined example">
                                    <a onclick="history.back()" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-rotate-left"></i> Kembali</a>
                                    <button type="submit" class="btn btn-sm btn-primary">Simpan <i class="fa-solid fa-floppy-disk"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <div id="errorCreate" class="mb-3" style="display:none;">
                            <div class="alert alert-danger" role="alert">
                                <div class="alert-icon"><i class="flaticon-danger text-danger"></i></div>
                                <div class="alert-text">
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-sm-3 align-self-center mb-0" for="select2Pasien">Pasien :</label>
                            <div class="col-sm-9">
                                <select id="select2Pasien" class="form-select" name="pasien_id">
                                    @if(isset($data->pasien_id))
                                    <option value="{{ $data->pasien_id }}">{{ $data->pasien->nama }}</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-sm-3 align-self-center mb-0" for="keluhan">Keluhan :</label>
                            <div class="col-sm-9">
                                <textarea name="keluhan" id="keluhan" cols="30" rows="4" class="form-control">{{ $data->keluhan ?? '' }}</textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-sm-3 align-self-center mb-0" for="select2Dokter">Dokter :</label>
                            <div class="col-sm-9">
                                <select id="select2Dokter" class="form-select" name="dokter_id">
                                    @if(isset($data->dokter_id))
                                    <option value="{{ $data->dokter_id }}">{{ $data->dokter->nama }}</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-sm-3 align-self-center mb-0" for="diagnosa">Diagnosa :</label>
                            <div class="col-sm-9">
                                <textarea name="diagnosa" id="diagnosa" cols="30" rows="4" class="form-control">{{ $data->diagnosa ?? '' }}</textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-sm-3 align-self-center mb-0" for="perawatan">Perawatan :</label>
                            <div class="col-sm-9">
                                <select name="perawatan[]" id="perawatan" multiple="multiple" class="form-select">
                                    @if(isset($perawatan))
                                    @foreach($perawatan as $p)
                                    <option value="{{ $p['id'] }}" selected>{{ $p['nama'] }}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-sm-3 align-self-center mb-0" for="keterangan">Keterangan :</label>
                            <div class="col-sm-9">
                                <textarea name="keterangan" id="keterangan" cols="30" rows="4" class="form-control">{{ $data->keterangan ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
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

        $('#select2Pasien').select2({
            dropdownParent: $('#select2Pasien').parent(),
            placeholder: "Cari Pasien",
            allowClear: true,
            ajax: {
                url: "{{ route('pasien.select2') }}",
                dataType: "json",
                cache: true,
                data: function(e) {
                    return {
                        q: e.term || '',
                        page: e.page || 1
                    }
                },
            },
        });
        $('#select2Dokter').select2({
            dropdownParent: $('#select2Dokter').parent(),
            placeholder: "Cari Dokter",
            allowClear: true,
            ajax: {
                url: "{{ route('dokter.select2') }}",
                dataType: "json",
                cache: true,
                data: function(e) {
                    return {
                        q: e.term || '',
                        page: e.page || 1
                    }
                },
            },
        });

        $('#perawatan').select2({
            dropdownParent: $('#perawatan').parent(),
            placeholder: "Cari Perawatan",
            allowClear: true,
            ajax: {
                url: "{{ route('perawatan.select2') }}",
                dataType: "json",
                cache: true,
                data: function(e) {
                    return {
                        q: e.term || '',
                        page: e.page || 1
                    }
                },
            },
        });

        $("#formStore").submit(function(e) {
            e.preventDefault();
            let form = $(this);
            let btnSubmit = form.find("[type='submit']");
            let btnSubmitHtml = btnSubmit.html();
            let url = form.attr("action");
            let data = new FormData(this);
            $.ajax({
                cache: false,
                processData: false,
                contentType: false,
                type: "POST",
                url: url,
                data: data,
                beforeSend: function() {
                    btnSubmit.addClass("disabled").html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...').prop("disabled", "disabled");
                },
                success: function(response) {
                    let errorCreate = $('#errorCreate');
                    errorCreate.css('display', 'none');
                    errorCreate.find('.alert-text').html('');
                    btnSubmit.removeClass("disabled").html(btnSubmitHtml).removeAttr("disabled");
                    if (response.status === "success") {
                        toastr.success(response.message, 'Success !');
                        setTimeout(function() {
                            if (response.redirect === "" || response.redirect === "reload") {
                                location.reload();
                            } else {
                                location.href = response.redirect;
                            }
                        }, 1000);
                    } else {
                        toastr.error((response.message ? response.message : "Please complete your form"), 'Failed !');
                        if (response.error !== undefined) {
                            errorCreate.removeAttr('style');
                            $.each(response.error, function(key, value) {
                                errorCreate.find('.alert-text').append('<span style="display: block">' + value + '</span>');
                            });
                        }
                    }
                },
                error: function(response) {
                    btnSubmit.removeClass("disabled").html(btnSubmitHtml).removeAttr("disabled");
                    toastr.error(response.responseJSON.message, 'Failed !');
                }
            });
        });
    });
</script>
@endsection