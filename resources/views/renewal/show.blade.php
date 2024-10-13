<x-layout>
    <head>
        <!-- Link the renewalbuttons.css only for this blade file -->
        <link rel="stylesheet" href="{{ asset('styles/css/renewalbuttons.css') }}">
    </head>

    <section class="main_content-profile">
        <header>
            <p>Renewal Request Details</p>
            <img src="https://st3.depositphotos.com/6672868/13701/v/450/depositphotos_137014128-stock-illustration-user-profile-icon.jpg" alt="Student Photo" />
        </header>

        <div class="profile_info">
            <div class="display_profile-info">
                <div class="profile_info-personal-details">
                    <p>Request Information</p>
                    <div class="profile_info-personal-details-header">
                    </div>
                </div>

                <div>
                    <p>Basic Details</p>
                    <p><strong>Name:</strong> {{ $renewalForm->user->name }}</p>
                    <p><strong>Student ID:</strong> {{ $renewalForm->student_number }}</p>
                    <p><strong>Attended Events:</strong> {{ $renewalForm->attended_events }}</p>
                    <p><strong>Shared Posts:</strong> {{ $renewalForm->shared_posts }}</p>
                    <p><strong>Duty Hours:</strong> {{ $renewalForm->duty_hours }}</p>
                    <p><strong>Approval Status:</strong> 
                        <span class="badge {{ $renewalForm->approval_status == 'approved' ? 'approved' : 'rejected' }}">
                            {{ ucfirst($renewalForm->approval_status) }}
                        </span>
                    </p>

                    <!-- Displaying Registration Fee Picture -->
                    @if ($renewalForm->registration_fee_picture)
                        <p><strong>Registration Fee Picture:</strong></p>
                        <img src="{{ asset('storage/'.$renewalForm->registration_fee_picture) }}" alt="Registration Fee Picture" class="image-preview">
                    @else
                        <p><strong>Registration Fee Picture:</strong> No image available</p>
                    @endif

                    <!-- Displaying Disbursement Method -->
                    @if ($renewalForm->disbursement_method)
                        <p><strong>Disbursement Method:</strong></p>
                        <img src="{{ asset('storage/'.$renewalForm->disbursement_method) }}" alt="Disbursement Method" class="image-preview">
                    @else
                        <p><strong>Disbursement Method:</strong> No image available</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Actions Section -->
        <div class="recent_activities">
            <p>Actions</p>

            <!-- Dropdown for Updating Approval Status -->
            <form action="{{ route('renewal.updateRenewal', $renewalForm->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="approval_status">Approval Status</label>
                    <select name="approval_status" class="form-control btn-update-renewal-dropdown" required>
                        <option value="approved" {{ $renewalForm->approval_status == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ $renewalForm->approval_status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <button type="submit" class="btn-update-renewal">Update</button>
            </form>

            <!-- Delete Button -->
            <form action="{{ route('renewal.deleteRenewal', $renewalForm->id) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-delete-renewal" onclick="return confirm('Are you sure you want to delete this request?');">Delete</button>
            </form>
        </div>
    </section>

    <!-- Include Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>
</x-layout>
