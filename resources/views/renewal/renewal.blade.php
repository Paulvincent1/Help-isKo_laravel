<!-- renewal.blade.php -->
<x-layout>
    <section class="main_content-employee-student">
        <header>
            <p>Renewal Request</p>
            <img src="https://st3.depositphotos.com/6672868/13701/v/450/depositphotos_137014128-stock-illustration-user-profile-icon.jpg" alt="Profile Icon" />
        </header>

        <div class="employee-student__main-table">
            <div class="employee-student__main-table-header">
                <h2>Renewal Requests Table</h2>
            </div>
            <div class="table_window">
                <table id="myTable">
                    <thead>
                        <tr>
                            <th>Photo</th>
                            <th>Name</th>
                            <th>Student ID</th>
                            <th>View</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($renewalForms as $form)
                        <tr>
                            <td>
                                <img src="https://st3.depositphotos.com/6672868/13701/v/450/depositphotos_137014128-stock-illustration-user-profile-icon.jpg" alt="Student Photo" style="width: 50px; height: 50px; border-radius: 50%;" />
                            </td>
                            <td>{{ $form->user->name }}</td>
                            <td>{{ $form->student_number }}</td>
                            <td>
                                <a href="{{ route('renewal.show', $form->id) }}" class="btn btn-success">View</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</x-layout>
