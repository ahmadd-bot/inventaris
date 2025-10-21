<x-main-layout :title-page="__('Edit Barang')">
    <form class="card" action="{{ route('barang.update', $barang->id) }}" method="POST" enctype="multipart/form-data">
        <div class="card-body">
            @method('PUT')
            @php
                $update = true;
            @endphp
            @include('barang.partials._form')
        </div>
    </form>
</x-main-layout>