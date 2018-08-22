<div class="form-group">
  <label for="give">{{ ucfirst($field) }}</label>
    <select 
      class="form-control" 
      id="{{ $field }}"
      name="{{ $field }}"
      >
      @foreach ($options as $option)
          <option
                  value="{{ $option->id }}"
                  {{ ($selected == $option->id ? "selected":"") }}
          >{{ $option->title }}</option>
      @endforeach
  </select>
</div>