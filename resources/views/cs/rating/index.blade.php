<x-layouts.app>
    <x-slot name="header">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Ratting Saya</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/cs/chat">Home</a></li>
                    <li class="breadcrumb-item active">Ratting Saya</li>
                </ol>
            </div>
        </div>
    </x-slot>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-danger card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-star mr-2"></i>Data Rating Pelayanan</h3>
                        <div class="card-tools">
                            <span class="badge badge-info">Total: {{ $totalRatings }}</span>
                            <span class="badge badge-success ml-1">
                                <i class="fas fa-star"></i>
                                Avg: {{ $averageRating ? number_format($averageRating, 1) : '0.0' }}
                            </span>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 5%">#</th>
                                        <th style="width: 15%">Sesi ID</th>
                                        <th style="width: 30%">Member</th>
                                        <th style="width: 20%">Rating</th>
                                        <th style="width: 20%">Tanggal Rating</th>
                                        <th style="width: 10%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($ratings as $index => $rating)
                                        <tr>
                                            <td>{{ $ratings->firstItem() + $index }}</td>
                                            <td>
                                                <span class="badge badge-secondary">#{{ $rating->id }}</span>
                                            </td>
                                            <td>
                                                <i class="fas fa-user-circle mr-1 text-primary"></i>
                                                {{ $rating->member->name ?? '-' }}
                                            </td>
                                            <td>
                                                @php
                                                    $ratingValue = (int) $rating->rating_pelayanan;
                                                    $badgeClass = $ratingValue >= 4 ? 'success' : ($ratingValue >= 3 ? 'warning' : 'danger');
                                                @endphp
                                                <span class="badge badge-{{ $badgeClass }} badge-lg">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <i class="fas fa-star{{ $i <= $ratingValue ? '' : '-o' }}"></i>
                                                    @endfor
                                                    {{ $ratingValue }}
                                                </span>
                                            </td>
                                            <td>
                                                <small>
                                                    <i class="far fa-clock mr-1"></i>
                                                    {{ $rating->rating_pelayanan_at ? \Carbon\Carbon::parse($rating->rating_pelayanan_at)->format('d M Y H:i') : '-' }}
                                                </small>
                                            </td>
                                            <td>
                                                <a href="{{ route('cs.sesi-chat.detail', $rating->id) }}" class="btn btn-sm btn-info" title="Lihat Detail Chat">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">
                                                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                                Belum ada data rating untuk akun CS ini
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if($ratings->hasPages())
                        <div class="card-footer clearfix">
                            <div class="float-right">
                                {{ $ratings->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
