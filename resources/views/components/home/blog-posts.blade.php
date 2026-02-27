{{-- resources/views/components/home/blog-posts.blade.php --}}
@if(class_exists('App\Models\Post'))
<section class="container mx-auto px-4 py-12 bg-gray-50">
    <div class="text-center mb-8">
        <span class="text-blue-600 font-semibold text-sm uppercase tracking-wider">Tech Insights</span>
        <h2 class="text-3xl font-bold mt-2">Latest From Our Blog</h2>
    </div>

    <div class="grid md:grid-cols-3 gap-6">
        @php $posts = App\Models\Post::latest()->limit(3)->get(); @endphp
        @forelse($posts as $post)
            <div class="bg-white rounded-xl overflow-hidden shadow-md hover:shadow-xl transition">
                <img src="{{ $post->featured_image ?? 'https://via.placeholder.com/400x250' }}" 
                     alt="{{ $post->title }}" 
                     class="w-full h-48 object-cover">
                <div class="p-6">
                    <div class="text-sm text-gray-500 mb-2">{{ $post->created_at->format('M d, Y') }}</div>
                    <h3 class="text-xl font-bold mb-2">{{ $post->title }}</h3>
                    <p class="text-gray-600 mb-4">{{ Str::limit($post->excerpt ?? $post->content, 100) }}</p>
                    <a href="{{ route('blog.show', $post->slug) }}" 
                       class="text-blue-600 font-semibold hover:text-blue-700 flex items-center gap-2">
                        Read More
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-3 text-center py-12">
                <p class="text-gray-500">No blog posts available.</p>
            </div>
        @endforelse
    </div>
</section>
@endif