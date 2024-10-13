<x-layout>
    <section class="main_content-profile">
        <header>
            <p>Update Renewal Request Status</p>
        </header>

        <form action="{{ route('renewal.updateRenewal', $renewalForm->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="approval_status">Approval Status</label>
                <select name="approval_status" required>
                    <option value="approved" {{ $renewalForm->approval_status == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ $renewalForm->approval_status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </section>
</x-layout>
