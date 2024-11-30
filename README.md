# Laravel Background Job Manager

**Laravel Background Job Manager** is a robust package that simplifies the management of background jobs in Laravel applications. With this package, you can:

- Execute any class method with parameters asynchronously.
- Use a dedicated dashboard panel to manage and monitor jobs.
- Integrate seamlessly into your application using a simple helper.

This tool is perfect for applications that require offloading tasks like notifications, data processing, or external API calls into the background to enhance performance and responsiveness.

---

## Table of Contents

1. [Installation](#installation)
2. [Configuration](#configuration)
3. [Panel](#panel)
4. [Helper Function](#helper-function)
5. [Example Usage](#example-usage)
6. [Running the Queue](#running-the-queue)
7. [Running the Queue Continuousl](#running-the-queue-continuousl)
8. [Contributing](#contributing)
9. [License](#license)

---

# Installation

To get started with the Laravel Background Job Manager, follow these steps:

1. **Install the package via Composer**:  
   Run the following command in your terminal to add the package to your project:

   ```bash
   composer require mralgorithm/job-runner
   ```
2. **Publish the configuration file**:
    The configuration file is essential for defining approved classes and methods, ensuring secure execution of background jobs. To publish the file, run the command below:

    ```bash
    php artisan vendor:publish --provider="Mralgorithm\\JobRunner\\JobRunnerServiceProvider"
   ```

    After publishing, the configuration file will be located in the config directory of your Laravel application. Be sure to customize it by specifying the classes and methods you wish to allow for background processing.

# Configuration

For security reasons, you need to explicitly specify the classes and methods that are allowed to be added to the job queue. This configuration is managed in the `config/jobrunner.php` file.

#### Configuring Classes and Methods

Open the config/jobrunner.php file and add your classes and their corresponding methods in the format below:

```php
return [
    Mralgorithm\JobRunner\TestClass::class => [
        'test',
        'testWithParam',
    ],
    // Add other approved classes and methods here
];
```

-The array keys should be the fully qualified class names (e.g., Mralgorithm\JobRunner\TestClass::class).-The array values should be an array of method names that can be executed for that class.

# Panel

The package includes a built-in dashboard panel that allows you to:

1. **Add New Jobs**: Easily specify the class, method, and parameters for the job you want to execute in    the background. You can also customize the job with the following options:
   - **Delay**: Set a delay for when the job should be executed.
   - **Priority**: Specify the job's priority to determine its execution order.
   - **Max Retries**: Define the maximum number of retry attempts for the job if it fails.

2. **Monitor Jobs**: View currently running jobs and check their status. Note that the status updates are not real-time; you need to refresh the page to see the latest information.

3. **Additional Features**:
   - **Pause**: Temporarily pause the execution of a job.
   - **Delete**: Remove a job from the list.
   - **Edit**: Modify job details as needed.

#### Accessing the Panel

To access the panel, go to `http://your-domain/mralgorithm`.

![Add new Job](https://iili.io/20ej13B.png)

![Jobs List](https://iili.io/20Nihu4.png)

# Helper Function

The Laravel Background Job Manager package includes global helper functions that you can use after installation to manage jobs programmatically. These functions make it easy to add, update, remove, and change the status of jobs directly from your code.

#### Available Helper Functions

1. **`addToJobRunner($className, $methodName, $params = [], $delay = 0, $priority = 0, $max_retries = 0)`**
   - Adds a new job to the job runner with the specified class, method, and parameters.
   - Options for customizing the job include delay(in seconds), priority, and maximum retry attempts.

2. **`updateJobRunner($className, $methodName, $params = [], $delay = 0, $priority = 0, $max_retries = 0, $job_id = 0)`**
   - Updates an existing job by specifying the new details and the job ID.

3. **`removeJobRunner($job_id)`**
   - Removes a job from the job runner using its job ID.

4. **`changeJobRunnerStatus($job_id, $status)`**
  - Changes the status of a job using its job ID. This function has specific conditions for which statuses can be changed:
     - You can change a job with a status of **"queued"** or **"retrying"** to **"paused"**.
     - You can change a job with a status of **"paused"** back to **"queued"**.


# Example Usage

To demonstrate how to use the Laravel Background Job Manager, we have created a `TestClass` that is included in the approved classes by default. You can use this class for testing job creation and management.

#### Sample Test Class Methods

Below are the available methods in `Mralgorithm\JobRunner\TestClass`:

1. **`test()`**
   - **No parameters**

2. **`testWithParam(string $param1)`**
   - **Parameters**: `string $param1`

3. **`NotAllowedFunction()`**
   - **No parameters**
   - This method is not included in the configuration file and cannot be used for job execution. Attempting to add it will result in an error.

#### Adding a New Job

To add a new job, you can use the `addToJobRunner` helper function like this:

```php
addToJobRunner(
    "Mralgorithm\JobRunner\TestClass", // Class name
    "testWithParam",                   // Method name
    ["param1"],                        // Parameters to pass to the method
    30,                                // Delay in seconds
    1,                                 // Priority (optional)
    1                                  // Max retries (optional)
);
```

#### Updating an Existing Job

To update an existing job, use the updateJobRunner helper function. Below is an example:

```php
updateJobRunner(
    "Mralgorithm\JobRunner\TestClass", // Class name
    "test",                            // Method name
    [],                                // Parameters (none for this method)
    30,                                // Delay in seconds
    1,                                 // Priority (optional)
    1,                                 // Max retries (optional)
    1                                 // Job ID to update
);
```

#### Removing a Job

To remove an existing job, you can use the removeJobRunner helper function. Here's an example:

```php
removeJobRunner(1); // Removes the job with ID 11
```

#### Changing Job Status

To change the status of a job, use the changeJobRunnerStatus helper function. Below is an example:

```php
changeJobRunnerStatus(10, 'paused'); // Pauses the job with ID 10
```

The changeJobRunnerStatus function takes the job ID and the desired status as arguments.
- You can change a job with a status of "queued" or "retrying" to "paused".
- You can also change a job with a status of "paused" back to "queued".

# Running the Queue

Once you've added jobs to the queue, you need to run the queue worker to process them. You can do this using the following Artisan command:

```bash
php artisan jobs:process
```

This command will process the jobs in the queue and execute the code you added.

# Running the Queue Continuously

To keep your Laravel queue worker running continuously, you can set up the following solutions depending on your operating system:

## For Linux: Using Supervisor

Supervisor is a process manager that ensures your Laravel queue worker runs continuously and restarts automatically if it stops.

**Step 1: Install Supervisor**

Run the following command to install Supervisor:

```bash
sudo apt-get install supervisor
```

**Step 2: Configure Supervisor**

Create a Supervisor configuration file for your Laravel queue worker:

```bash
sudo nano /etc/supervisor/conf.d/laravel-queue.conf
```

Add the following configuration:

```bash
[program:laravel-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/your/laravel/project/artisan jobs:process
autostart=true
autorestart=true
user=your-server-username
numprocs=1
redirect_stderr=true
stdout_logfile=/path/to/your/laravel/project/storage/logs/laravel-queue.log
```

-Replace /path/to/your/laravel/project with the path to your Laravel project.
-Set user to the appropriate user for running the queue.
-The stdout_logfile ensures that output is logged to a file for easy debugging.

**Step 3: Update Supervisor and Start the Program**

Reload Supervisor to apply the new configuration:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-queue:*
```

Now, your Laravel queue worker will run continuously on Linux.

## For Windows: Using Task Scheduler or NSSM

### Option 1: Using Task Scheduler

**Step 1: Create a Batch File**

Create a batch file (run_jobs.bat) in your Laravel project directory:

```batch
@echo off
cd /d C:\path\to\your\laravel\project
php artisan jobs:process
```

**Step 2: Configure Task Scheduler**

1. Open Task Scheduler and create a new basic task.
2. Set the trigger to "When the computer starts" or configure it to repeat periodically.
3. Choose "Start a Program" as the action and browse to your run_jobs.bat.
4. Check "Run with highest privileges" for reliable execution.

This will ensure your batch file runs when the system starts or at set intervals.

## Option 2: Using NSSM (Non-Sucking Service Manager)

**Step 1: Download and Install NSSM**

Download NSSM from [https://nssm.cc/download](https://nssm.cc/download) and install it.

**Step 2: Create a Windows Service**

Run the following command to create a service:

```powershell
nssm install LaravelQueueWorker
```

In the NSSM window:
1. Set Application path to php.exe.
2. Set Arguments to artisan jobs:process.
3. Set Start directory to your Laravel project directory.
4. Click Install service.

**Step 3: Start the Service**

Run this command to start your service:

```powershell
net start LaravelQueueWorker
```

This method will ensure that the queue worker runs as a background service and restarts on system reboots.

# Contributing

We welcome contributions to the Laravel Background Job Manager package! Your help can make this package even better. Below are guidelines and best practices for contributing.

#### How to Contribute

1. **Fork the Repository**: Click the **Fork** button at the top right of the repository page to create a copy of the repository in your GitHub account.
2. **Clone Your Fork**: Clone your forked repository to your local machine:
    ```bash
    git clone https://github.com/mralgorithm2000/job-runner.git
    ```
3. **Create a Branch**: Create a new branch for your feature or bug fix:
    ```bash
    git checkout -b feature-branch-name
    ```
4. **Make Your Changes**: Implement your feature or bug fix and ensure your code follows the style and conventions used in the project.
5. **Test Your Changes**: Run any tests to verify your changes work as expected.
6. **Commit and Push**: Commit your changes and push them to your forked repository:
    ```bash
    git add .
    git commit -m "Description of your changes"
    git push origin feature-branch-name
    ```
7. **Create a Pull Request**: Go to the original repository and click on **New Pull Request**. Select your feature branch and submit the pull request for review.

# License

The Laravel Background Job Manager package is open-source software licensed under the [MIT License](https://opensource.org/licenses/MIT).

#### Summary of the MIT License:

- **Permissions**: You are free to use, copy, modify, merge, publish, distribute, sublicense, and even sell copies of the software.
- **Conditions**: You must include the original license and copyright notice in any distribution of the software.
- **Limitations**: The software is provided "as is," without warranty of any kind. The authors are not liable for any damages arising from the use of the software.

For more details, refer to the [LICENSE](./LICENSE) file in this repository.

---