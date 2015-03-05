@extends('layouts.notes')

@section('table')
<table class="table table-bordered table-hover table-striped display right-two-blank" id="archives_table" width="100%">
    <thead>
        <tr>
            <th>Last Updated</th>
            <th>Created</th>
            <th>Archives<input type="search" class="round-button full-round-button" placeholder="search archives" aria-controls="archives_table"></th>
        </tr>
    </thead>
</table>
@stop

@section('sidebar-menu')
<div class="menu-slide-out">
    <span class="hamburger-icon"><span class="glyphicon glyphicon-menu-hamburger" aria-hidden="true"></span></span>
    <ul>
        <li class="create-permanent-user"><a class="full-round-button round-button">Permanent User</a></li>
        <li><a class="full-round-button round-button" href="/">New Note</a></li>
        <li><a class="full-round-button round-button" href="notes">All Notes</a></li>
        @if (Auth::user()->is_temporary == 0)
        <li><a class="full-round-button round-button" href="logout">Logout</a></li>
        @endif
    </ul>
</div>
@stop

@section('scripts')
<script src="js/datatables/archives.js"></script>
@stop
