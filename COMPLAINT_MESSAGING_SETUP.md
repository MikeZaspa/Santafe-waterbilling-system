# Two-Way Complaint Messaging System - Implementation Guide

## Overview
This implementation provides a messenger-like interface for both consumers and admins to handle complaint conversations. All messages are stored in a threaded conversation view.

## Changes Made

### 1. Database Changes
- **Migration Created**: `2026_02_27_000000_add_messaging_to_complaints.php`
  - Added `subject` field to `complaints` table
  - Added `status` field (open, in_progress, resolved, closed) to `complaints` table
  - Added `last_message_at` timestamp to `complaints` table
  - Created `complaint_messages` table for storing conversation messages

**Run the migration:**
```bash
php artisan migrate
```

### 2. Models Created/Updated

#### ComplaintMessage Model
- Location: `app/Models/ComplaintMessage.php`
- Stores individual messages in the conversation thread
- Relations: belongs to Complaint

#### Updated Complaint Model
- Added relationships to ComplaintMessages
- Added helper methods: `addMessage()`, `markMessagesAsRead()`

### 3. Controllers Updated/Created

#### ConsumerComplaintController (Updated)
- `index()` - Shows complaint list
- `store()` - Creates new complaint with initial message
- `show()` - Returns complaint with messages as JSON
- `addReply()` - Adds a reply to existing complaint
- `update()` - Updates complaint status or subject
- `destroy()` - Deletes complaint and all messages
- `attachment()` - Serves attachments

#### AdminComplaintController (New)
- Location: `app/Http/Controllers/Admin/AdminComplaintController.php`
- `index()` - Lists all complaints
- `show()` - Returns complaint with messages as JSON
- `addReply()` - Admin adds reply to complaint
- `updateStatus()` - Updates complaint status
- `attachment()` - Serves attachments

### 4. Routes Added

**Consumer Routes** (in `routes/web.php`):
```php
Route::middleware('auth:consumer')->prefix('consumer/complaints')->name('consumer.complaints.')->group(function () {
    Route::get('/', [ConsumerComplaintController::class, 'index'])->name('index');
    Route::post('/', [ConsumerComplaintController::class, 'store'])->name('store');
    Route::get('/{complaint}', [ConsumerComplaintController::class, 'show'])->name('show');
    Route::post('/{complaint}/reply', [ConsumerComplaintController::class, 'addReply'])->name('reply');
    Route::put('/{complaint}', [ConsumerComplaintController::class, 'update'])->name('update');
    Route::delete('/{complaint}', [ConsumerComplaintController::class, 'destroy'])->name('destroy');
    Route::get('/{complaint}/attachment', [ConsumerComplaintController::class, 'attachment'])->name('attachment');
    Route::get('/{complaint}/message/{messageId}/attachment', [ConsumerComplaintController::class, 'attachment'])->name('message.attachment');
});
```

**Admin Routes** (in `routes/web.php`):
```php
Route::middleware('admin.auth')->prefix('admin/complaints')->name('admin.complaints.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\AdminComplaintController::class, 'index'])->name('index');
    Route::get('/{complaint}', [\App\Http\Controllers\Admin\AdminComplaintController::class, 'show'])->name('show');
    Route::post('/{complaint}/reply', [\App\Http\Controllers\Admin\AdminComplaintController::class, 'addReply'])->name('reply');
    Route::put('/{complaint}/status', [\App\Http\Controllers\Admin\AdminComplaintController::class, 'updateStatus'])->name('status');
    Route::get('/{complaint}/attachment', [\App\Http\Controllers\Admin\AdminComplaintController::class, 'attachment'])->name('attachment');
    Route::get('/{complaint}/message/{messageId}/attachment', [\App\Http\Controllers\Admin\AdminComplaintController::class, 'attachment'])->name('message.attachment');
});
```

### 5. Views Updated/Created

#### Consumer View
- **File**: `resources/views/auth/consumer-complaints.blade.php`
- Updated to show complaints list instead of single chatbox
- New "New Complaint" modal for creating complaints
- New "Complaint Conversation" modal for viewing/replying to conversations
- JavaScript handles loading complaints and sending replies via AJAX

#### Admin View
- **File**: `resources/views/auth/admin-dashboard.blade.php`
- Updated complaints table to show subject, status, and last message time
- Added "Conversation" button for each complaint
- Included new admin conversation modal component

#### Admin Conversation Modal Component
- **File**: `resources/views/admin/complaints/conversation-modal.blade.php`
- Modal for admins to view and reply to complaints
- Includes status dropdown for updating complaint status
- Messages displayed with sender info and timestamps
- Support for file attachments

## Features

### Consumer Features
1. **Create New Complaint**
   - Add subject and message
   - Optional file attachment
   - Complaint shows in list

2. **View Complaint Conversation**
   - Click on any complaint to open conversation
   - See all previous messages from both consumer and admin
   - See attachments with download buttons

3. **Reply to Complaint**
   - Send messages in the conversation thread
   - Optional file attachments
   - Messages appear immediately

4. **Complaint Status**
   - Can see current status of complaint
   - Status updates shown in badges

### Admin Features
1. **View All Complaints**
   - List shows consumer name, meter number, subject, status
   - Shows last message time
   - Badge indicates complaint status

2. **Conversation View**
   - Click "Conversation" button to open full conversation thread
   - See all messages in order
   - See consumer and admin names for each message

3. **Reply to Complaints**
   - Send messages in the conversation
   - Change complaint status (open, in_progress, resolved, closed)
   - Attach files to replies

4. **Message Threading**
   - All messages are organized in a thread
   - Clear distinction between consumer and admin messages
   - Timestamps for all messages

## Usage Examples

### For Consumers
1. Go to Complaints page
2. Click "New Complaint" button
3. Fill subject and message
4. Optionally attach a file
5. Click "Submit Complaint"
6. To reply, click on the complaint in the list
7. The conversation modal opens
8. Type your reply and click "Send Reply"

### For Admins
1. Go to Admin Dashboard
2. Click the Complaints card/section
3. Find the complaint in the table
4. Click "Conversation" button
5. Read the conversation thread
6. Type your reply
7. Optionally select a new status
8. Attach file if needed
9. Click "Send Reply"

## Database Structure

### complaints table
```
id
consumer_id (foreign key to admin_consumers)
subject (string, nullable)
message (text)
attachment_path (string, nullable)
status (enum: open, in_progress, resolved, closed)
last_message_at (timestamp, nullable)
created_at
updated_at
```

### complaint_messages table
```
id
complaint_id (foreign key to complaints)
sender_type (enum: consumer, admin)
sender_name (string)
message (text)
attachment_path (string, nullable)
is_read (boolean)
created_at
updated_at
```

## Security Notes
- Consumer complaints are only accessible by the consumer who created them
- Admin endpoints require admin authentication
- CSRF protection is enabled on all forms
- File uploads are validated and stored securely
- User input is escaped to prevent XSS attacks

## API Endpoints

### Consumer Endpoints
- `GET /consumer/complaints` - List complaints
- `POST /consumer/complaints` - Create complaint
- `GET /consumer/complaints/{id}` - Get complaint with messages (JSON)
- `POST /consumer/complaints/{id}/reply` - Add reply
- `GET /consumer/complaints/{id}/attachment` - Download attachment

### Admin Endpoints
- `GET /admin/complaints` - List all complaints
- `GET /admin/complaints/{id}` - Get complaint with messages (JSON)
- `POST /admin/complaints/{id}/reply` - Add reply and update status
- `PUT /admin/complaints/{id}/status` - Update status only
- `GET /admin/complaints/{id}/attachment` - Download attachment

## Future Enhancements
1. Real-time notifications when new messages arrive
2. Typing indicators
3. Message notifications via email
4. Search and filter complaints
5. Assign complaints to specific staff members
6. Complaint categories/tags
7. Message reactions/emoji support
8. Complaint resolution templates
