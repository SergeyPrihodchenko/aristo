<div style="
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 16px;
    background: #ffffff;
    max-width: 512px;
    box-sizing: border-box;
">

    {{-- PHOTO --}}
    @if(!empty($photo))
        <img
            src="{{ \Illuminate\Support\Facades\Storage::url($photo) }}"
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

    {{-- MESSAGE --}}
    <div style="
        font-size: 14px;
        line-height: 1.5;
        white-space: pre-wrap;
        color: #000000;
        font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
    ">
        {!! nl2br(e($message ?? '')) !!}
    </div>

</div>