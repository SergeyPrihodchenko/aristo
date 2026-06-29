@php
    $state = $getState();
    $photo = $state['photo'];
    if(!empty($photo)) {
        if(!is_string($photo[array_key_first($photo)])
                && 
            Livewire\Features\SupportFileUploads\TemporaryUploadedFile::class
                == 
            $photo[array_key_first($photo)]::class) 
        {
            $photo = $photo[array_key_first($photo)]->temporaryUrl();
        } else {
            $photo = asset('storage/tg-posts/'.$photo[array_key_first($photo)]); 
        }
    } else {
        $photo = '';
    }
    $message = $state['message'];
@endphp

<div style="
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 16px;
    background: #ffffff;
    max-width: 512px;
    box-sizing: border-box;
">

    @if($photo)
        <img
            src="{{ $photo }}"
            style="
                border-radius: 8px;
                margin-bottom: 12px;
                max-height: 288px;
                width: 100%;
                object-fit: cover;
                display: block;
            "
        />
    @endif

    <div style="
        font-size: 14px;
        line-height: 1.5;
        white-space: pre-wrap;
        color: #000;
        font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial;
    ">
        {!! nl2br(e($message ?? '')) !!}
    </div>

</div>