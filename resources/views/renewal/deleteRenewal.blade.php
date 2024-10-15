<x-layout>
    <section class="main_content-profile">
        <header>
            <p>Delete Renewal Request</p>
        </header>

        <form action="{{ route('renewal.deleteRenewal', $renewalForm->id) }}" method="POST">
            @csrf
            @method('DELETE')

            <p>Are you sure you want to delete this request?</p>
            <button type="submit" class="btn btn-danger">Delete</button>
        </form>
    </section>
</x-layout>
