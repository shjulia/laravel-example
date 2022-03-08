@extends('admin.users.edit.edit')
@section('edit-content')
    <form method="POST" action="{{ route('admin.users.edit.position', $user) }}">
        @csrf
        <input type="hidden" name="user_id" value="{{ $user->id }}" />
        <div class="form-group">
            <h5>Select Position</h5>
            <select
                id="position"
                class="form-control{{ $errors->has('position') ? ' is-invalid' : '' }}"
                name="position"
            >
                @foreach ($groupedPositions as $title => $positions)
                    <option value="{{ $title }}" disabled>{{ $title }}</option>
                    @foreach ($positions as $position)
                        <option value="{{ $position->id }}" @if($position->id == old('position', $user->specialist->position_id)) selected @endif>
                            {{ " -- " . $position->title }}
                        </option>
                        @if (!$position->children->isEmpty())
                            @foreach ($position->children as $child)
                                <option value="{{ $child->id }}" @if($child->id == old('position', $user->specialist->position_id)) selected @endif>
                                    {{ " ---- " . $child->title }}
                                </option>
                            @endforeach
                        @endif
                    @endforeach
                @endforeach
            </select>
            @if ($errors->has('position'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('position') }}</strong>
                </span>
            @endif
        </div>

        <div class="form-group">
            <button type="submit" class="btn form-button">Edit</button>
        </div>
    </form>
@endsection
