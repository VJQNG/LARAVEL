<h1>{{ $post->title }}</h1>
<p>{{ $post->content }}</p>

<hr>

<div>
    <h3>Archivos Adjuntos:</h3>
    @foreach($post->attachments as $attachment)
        <div>
            <a href="{{ asset('storage/' . $attachment->path) }}" target="_blank">
                {{ $attachment->original_name }}
            </a>
            
            <form action="{{ route('attachments.destroy', $attachment) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit">Eliminar</button>
            </form>
        </div>
    @endforeach
</div>
