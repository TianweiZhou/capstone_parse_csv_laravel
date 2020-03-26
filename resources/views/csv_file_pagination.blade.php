@extends('csv_file')

@section('csv_data')

    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th>ACNum</th>
            <th>FirstName</th>
            <th>LastName</th>
            <th>DOB</th>
            <th>Gender</th>
            <th>ClubCode</th>
            <th>City</th>
            <th>Phone</th>
            <th>Email</th>
            <th>ClubAffiliationSince</th>
        </tr>
        </thead>
        <tbody>
        @foreach($data as $row)
            <tr>
                <td>{{ $row->ACNum }}</td>
                <td>{{ $row->FirstName }}</td>
                <td>{{ $row->LastName}}</td>
                <td>{{ $row->DOB }}</td>
                <td>{{ $row->AthleteGender }}</td>
                <td>{{ $row->ClubCode }}</td>
                <td>{{ $row->City }}</td>
                <td>{{ $row->Phone }}</td>
                <td>{{ $row->AthleteEmail}}</td>
                <td>{{ $row->ClubAffiliationSince}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>


@endsection
