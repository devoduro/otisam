{{-- Suspend / reactivate control for one account. Expects $account (User). --}}
@php $mayChange = auth()->user()->can($account->isStaff() ? 'staff.manage' : 'users.suspend') && !$account->is(auth()->user()); @endphp
@if ($mayChange)
    @if ($account->isActive())
        <form method="POST" action="{{ route('admin.accounts.suspend', $account) }}" class="d-flex flex-column flex-sm-row gap-2 align-items-sm-end">
            @csrf
            <div class="flex-grow-1">
                <label for="reason-{{ $account->id }}" class="form-label small fw-semibold">Reason for suspending</label>
                <input id="reason-{{ $account->id }}" name="reason" class="form-control form-control-sm" maxlength="300" required>
            </div>
            <button class="btn btn-outline-danger btn-sm" type="submit" data-confirm="Suspend {{ $account->name }}? They will be signed out immediately.">Suspend account</button>
        </form>
    @else
        <form method="POST" action="{{ route('admin.accounts.activate', $account) }}">
            @csrf
            <button class="btn btn-outline-success btn-sm" type="submit">Reactivate account</button>
        </form>
    @endif
@endif
