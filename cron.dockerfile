# Use a lightweight base image
FROM alpine:latest

# Set working directory
WORKDIR /var/www/html

# Install necessary packages
RUN apk --update add \
    curl \
    bash \
    && rm -rf /var/cache/apk/*

# Copy the crontab file into the container
COPY ./crontab /etc/cron.d/laravel-scheduler

# Give execution rights to the cron job
RUN chmod 0644 /etc/cron.d/laravel-scheduler

# Create the log file to be able to run tail
RUN touch /var/log/cron.log

# Run cron in the foreground to keep the container running
CMD ["crond", "-f"]
