<select name="agence_id" class="form-select js-single" style="width: 150px;" id="agence_id" required aria-required="true">
    <option value=""></option>
    @foreach ($agencesUser as $agenceUser)
        <option value="{{ $agenceUser->agence->id }}">{{ $agenceUser->agence->NomAgence }}
        </option>
    @endforeach
</select>
