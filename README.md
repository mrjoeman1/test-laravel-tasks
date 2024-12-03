
## Task Description

Create a very simple Laravel web application for task management:

- Create task (info to save: task name, priority, timestamps)
- Edit task
- Delete task
- Reorder tasks with drag and drop in the browser. Priority should automatically be updated based on this. #1 priority goes at top, #2 next down and so on.
- Tasks should be saved to a mysql table.
- BONUS POINT: add project functionality to the tasks. User should be able to select a project from a dropdown and only view tasks associated with that project.

## What i have used to implement it?
- Laravel
- Laravel Sail
- Inertia
- Vue 3
- Bootstrap CSS

The main idea during development was to make it as simple as possible (KISS), without any over-engineering on that small task (YAGNI), but keep the code robust, clean and beautiful and make UX pretty smooth and pleasure.
I think i managed to do that :)

## Requirements

- Composer
- Docker

## How to run

```
composer install
./vendor/bin/sail up
./vendor/bin/sail php artisan migrate
./vendor/bin/sail php artisan db:seed
./vendor/bin/sail composer run dev
OR
./vendor/bin/sail num run build
```

