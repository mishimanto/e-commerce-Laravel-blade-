@props(['productId', 'productName'])

<div x-data="reviewModal({{ $productId }})" 
     x-show="open" 
     x-cloak
     @keydown.escape.window="closeModal"
     class="fixed inset-0 z-[9999] overflow-y-auto"
     aria-labelledby="modal-title" 
     role="dialog" 
     aria-modal="true"
     style="display: none;">
    
    {{-- Backdrop --}}
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-900 bg-opacity-75 backdrop-blur-sm"
         @click="closeModal">
    </div>

    {{-- Modal Panel --}}
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         class="relative flex items-center justify-center min-h-screen p-4">
        
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            
            {{-- Header --}}
            <!-- <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between rounded-t-2xl z-10">
                <h3 class="text-xl font-bold text-gray-900" id="modal-title">
                    Write a Review for <span class="text-blue-600">{{ $productName }}</span>
                </h3>
                <button @click="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div> -->

            {{-- Body --}}
            <div class="p-6">
                <form @submit.prevent="submitReview">
                    {{-- Rating --}}
                    <div class="mb-6 flex justify-center">                        
                        <!-- <label class="block text-sm font-medium text-gray-700 mb-2">
                            Your Rating <span class="text-red-500">*</span>
                        </label> -->
                        <div class="flex flex-col items-center sm:items-start">
                            <div class="flex flex-row-reverse justify-center gap-1 mb-2">
                                <template x-for="star in [5,4,3,2,1]" :key="star">
                                    <button type="button"
                                            @click="form.rating = star"
                                            class="p-1 focus:outline-none group">
                                        <svg class="w-8 h-8 sm:w-10 sm:h-10 transition-all duration-200 transform hover:scale-110" 
                                             :class="star <= form.rating ? 'text-yellow-400' : 'text-gray-300 hover:text-yellow-200'"
                                             fill="currentColor" 
                                             viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                    </button>
                                </template>
                            </div>
                            <!-- <span x-show="form.rating > 0" class="text-sm text-gray-600" x-text="'You selected ' + form.rating + ' star' + (form.rating > 1 ? 's' : '')"></span>
                            <span x-show="form.rating === 0" class="text-sm text-red-500">Please select a rating</span> -->
                        </div>
                    </div>
                     
                    @guest
                    <div class="mb-4">
                        <input type="text" 
                                       x-model="form.guest_name"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500"
                                       placeholder="Your Name (For guest user)">
                    </div>

                    <div class="mb-4">
                        <input type="email" 
                                       x-model="form.guest_email"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500"
                                       placeholder="Your Email (For guest user)">
                    </div>
                    @endguest


                    {{-- Review Title --}}
                    <div class="mb-4">
                        <!-- <label for="review_title" class="block text-sm font-medium text-gray-700 mb-1">
                            Review Title
                        </label> -->
                        <input type="text" 
                               id="review_title"
                               x-model="form.title"
                               maxlength="255"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Summarize your experience">
                    </div>

                    {{-- Review Comment --}}
                    <div class="mb-4">
                        <!-- <label for="review_comment" class="block text-sm font-medium text-gray-700 mb-1">
                            Your Review <span class="text-red-500">*</span>
                        </label> -->
                        <textarea id="review_comment"
                                  x-model="form.comment"
                                  rows="4"
                                  minlength="10"
                                  maxlength="2000"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                  placeholder="Share your experience with this product..."></textarea>
                        <div class="flex justify-end mt-1">
                            <span class="text-xs text-gray-500" x-text="form.comment.length + '/2000'"></span>
                        </div>
                    </div>

                    {{-- Pros & Cons --}}
                    <!-- <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Pros
                            </label>
                            <div class="space-y-2">
                                <template x-for="(pro, index) in form.pros" :key="index">
                                    <div class="flex items-center gap-2">
                                        <input type="text" 
                                               x-model="form.pros[index]"
                                               class="flex-1 px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                               placeholder="e.g., Great battery life">
                                        <button type="button" @click="removePro(index)" class="text-red-500 hover:text-red-700">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </template>
                                <button type="button" 
                                        @click="addPro"
                                        class="text-sm text-green-600 hover:text-green-700 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Add Pro
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Cons
                            </label>
                            <div class="space-y-2">
                                <template x-for="(con, index) in form.cons" :key="index">
                                    <div class="flex items-center gap-2">
                                        <input type="text" 
                                               x-model="form.cons[index]"
                                               class="flex-1 px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                               placeholder="e.g., Slightly heavy">
                                        <button type="button" @click="removeCon(index)" class="text-red-500 hover:text-red-700">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </template>
                                <button type="button" 
                                        @click="addCon"
                                        class="text-sm text-red-600 hover:text-red-700 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Add Con
                                </button>
                            </div>
                        </div>
                    </div> -->

                    {{-- Image Upload --}}
                    <!-- <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Upload Images (Max 5)
                        </label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-blue-400 transition-colors">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="review-images" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                        <span>Upload files</span>
                                        <input id="review-images" 
                                               type="file" 
                                               multiple 
                                               accept="image/*"
                                               @change="handleImageUpload"
                                               class="sr-only">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG, JPEG up to 2MB each</p>
                            </div>
                        </div>
                        
                        {{-- Image Previews --}}
                        <div x-show="imagePreviews.length > 0" class="mt-3 grid grid-cols-5 gap-2">
                            <template x-for="(preview, index) in imagePreviews" :key="index">
                                <div class="relative group">
                                    <img :src="preview" class="w-full h-16 object-cover rounded-lg border border-gray-200">
                                    <button type="button" 
                                            @click="removeImage(index)"
                                            class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full p-0.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div> -->

                    {{-- Guest User Info --}}
                    <!-- @guest
                    <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Your Name <span class="text-red-500">*</span></label>
                                <input type="text" 
                                       x-model="form.guest_name"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500"
                                       placeholder="Your Name">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Your Email <span class="text-red-500">*</span></label>
                                <input type="email" 
                                       x-model="form.guest_email"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500"
                                       placeholder="Your Email">
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Your email will not be published. Required fields are marked *</p>
                    </div>
                    @endguest -->

                    {{-- Error Display --}}
                    <div x-show="errors.length > 0" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                        <p class="text-sm font-medium text-red-800 mb-1">Please fix the following errors:</p>
                        <ul class="list-disc list-inside">
                            <template x-for="error in errors" :key="error">
                                <li class="text-xs text-red-600" x-text="error"></li>
                            </template>
                        </ul>
                    </div>

                    {{-- Footer Buttons --}}
                    <div class="mt-6 flex flex-col sm:flex-row gap-3 justify-end">
                        <button type="button" 
                                @click="closeModal"
                                class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                                :disabled="submitting">
                            <span x-show="!submitting">Submit Review</span>
                            <span x-show="submitting" class="flex items-center gap-2">
                                <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Submitting...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>

<script>
function reviewModal(productId) {
    return {
        open: false,
        productId: productId,
        submitting: false,
        errors: [],
        imagePreviews: [],
        form: {
            rating: 0,
            title: '',
            comment: '',
            pros: [],
            cons: [],
            images: [],
            guest_name: '',
            guest_email: ''
        },
        
        init() {
            // Make modal accessible globally
            if (typeof window.reviewModals === 'undefined') {
                window.reviewModals = [];
            }
            window.reviewModals.push(this);
        },
        
        openModal() {
            this.open = true;
            document.body.style.overflow = 'hidden';
        },
        
        closeModal() {
            this.open = false;
            document.body.style.overflow = '';
            this.resetForm();
        },
        
        resetForm() {
            this.form = {
                rating: 0,
                title: '',
                comment: '',
                pros: [],
                cons: [],
                images: [],
                guest_name: '',
                guest_email: ''
            };
            this.imagePreviews = [];
            this.errors = [];
        },
        
        addPro() {
            this.form.pros.push('');
        },
        
        removePro(index) {
            this.form.pros.splice(index, 1);
        },
        
        addCon() {
            this.form.cons.push('');
        },
        
        removeCon(index) {
            this.form.cons.splice(index, 1);
        },
        
        handleImageUpload(event) {
            const files = Array.from(event.target.files);
            const maxFiles = 5;
            
            if (this.form.images.length + files.length > maxFiles) {
                if (window.showNotification) {
                    window.showNotification('error', `You can only upload up to ${maxFiles} images`, 'Upload Error');
                }
                return;
            }
            
            files.forEach(file => {
                if (file.size > 2 * 1024 * 1024) {
                    if (window.showNotification) {
                        window.showNotification('error', `${file.name} is too large. Maximum size is 2MB`, 'File Too Large');
                    }
                    return;
                }
                
                if (!file.type.startsWith('image/')) {
                    if (window.showNotification) {
                        window.showNotification('error', `${file.name} is not an image file`, 'Invalid File');
                    }
                    return;
                }
                
                this.form.images.push(file);
                
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.imagePreviews.push(e.target.result);
                };
                reader.readAsDataURL(file);
            });
        },
        
        removeImage(index) {
            this.form.images.splice(index, 1);
            this.imagePreviews.splice(index, 1);
        },
        
        validateForm() {
            const errors = [];
            
            if (this.form.rating === 0) {
                errors.push('Please select a rating');
            }
            
            if (!this.form.comment || this.form.comment.length < 10) {
                errors.push('Review must be at least 10 characters long');
            }
            
            @guest
            if (!this.form.guest_name || this.form.guest_name.trim() === '') {
                errors.push('Please enter your name');
            }
            
            if (!this.form.guest_email || this.form.guest_email.trim() === '') {
                errors.push('Please enter your email');
            } else if (!/^\S+@\S+\.\S+$/.test(this.form.guest_email)) {
                errors.push('Please enter a valid email address');
            }
            @endguest
            
            return errors;
        },
        
        submitReview() {
            const errors = this.validateForm();
            if (errors.length > 0) {
                this.errors = errors;
                
                // Show first error as notification
                if (window.showNotification) {
                    window.showNotification('error', errors[0], 'Validation Error');
                }
                return;
            }
            
            this.submitting = true;
            this.errors = [];
            
            const formData = new FormData();
            formData.append('product_id', this.productId);
            formData.append('rating', this.form.rating);
            formData.append('title', this.form.title);
            formData.append('comment', this.form.comment);
            
            const validPros = this.form.pros.filter(p => p && p.trim() !== '');
            const validCons = this.form.cons.filter(c => c && c.trim() !== '');
            
            if (validPros.length > 0) {
                formData.append('pros', JSON.stringify(validPros));
            }
            
            if (validCons.length > 0) {
                formData.append('cons', JSON.stringify(validCons));
            }
            
            @guest
            formData.append('is_guest', 'true');
            formData.append('guest_name', this.form.guest_name);
            formData.append('guest_email', this.form.guest_email);
            @endguest
            
            this.form.images.forEach((image, index) => {
                formData.append(`images[${index}]`, image);
            });
            
            fetch('{{ route("reviews.store") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                this.submitting = false;
                
                if (data.success) {
                    // Show success notification
                    if (window.showNotification) {
                        window.showNotification(
                            'success', 
                            data.message, 
                            'Thanks for your review!',
                            { duration: 5000, showProgress: false }
                        );
                    }
                    
                    this.closeModal();
                    
                    // Reload after notification
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    if (data.errors) {
                        console.log('Validation errors:', data.errors);
                        this.errors = Object.values(data.errors).flat();
                        
                        // Show first error as notification
                        if (this.errors.length > 0 && window.showNotification) {
                            window.showNotification('error', this.errors[0], 'Validation Error');
                        }
                    } else {
                        this.errors = [data.message || 'Failed to submit review'];
                        if (window.showNotification) {
                            window.showNotification('error', data.message || 'Failed to submit review', 'Error');
                        }
                    }
                }
            })
            .catch(error => {
                this.submitting = false;
                this.errors = ['An error occurred. Please try again.'];
                
                if (window.showNotification) {
                    window.showNotification('error', 'An error occurred. Please try again.', 'Error');
                }
                
                console.error('Error:', error);
            });
        }
    }
}
</script>