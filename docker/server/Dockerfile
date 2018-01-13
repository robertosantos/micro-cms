FROM php:7.0-apache
MAINTAINER Roberto Santos <r_g_santos@hotmail.com>
WORKDIR "/application"

VOLUME ["/application/storage/log"]

# update and other stuff
RUN apt-get update && \
    apt-get -y install git vim wget \
    libmemcached11 libmemcachedutil2 libmemcached-dev && \
    apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

# Add mirror php7.0
RUN echo "deb http://packages.dotdeb.org jessie all" >> /etc/apt/sources.list.d/dotdeb.org.list && \
    echo "deb-src http://packages.dotdeb.org jessie all" >> /etc/apt/sources.list.d/dotdeb.org.list && \
    wget -O- http://www.dotdeb.org/dotdeb.gpg | apt-key add -

# PHP Extension
RUN apt-get update && apt-get -y install php7.0-memcached
RUN docker-php-ext-install pdo pdo_mysql

# Set Apache Rewrite
RUN a2enmod rewrite
COPY 000-default.conf /etc/apache2/sites-enabled/
COPY 000-default.conf /etc/apache2/sites-available/

# Alter permission for write in file
RUN chown -R www-data:www-data /application/storage

# Configure Xdebug
RUN yes | pecl install xdebug \
    && echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.auto_trace = 0" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.cli_color = 1" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.collect_assignments = 1" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.collect_includes = 1" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.collect_params = 1" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.collect_return = 1" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.collect_vars = 1" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.coverage_enable = 1" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xxdebug.default_enable = 1" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.dump.COOKIE" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.dump.ENV" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.dump.FILES" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.dump.GET" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.dump.POST" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.dump.REQUEST" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.dump.SERVER" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.dump.SESSION" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.dump_globals = 1" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.dump_once = 1" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.dump_undefined = 1" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.extended_info = 1" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.file_link_format" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.force_display_errors = 1" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.force_error_reporting = 1" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.halt_level = 0" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.idekey = PHPSTORM" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.max_nesting_level = 256" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.max_stack_frames = -1" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.overload_var_dump = 2" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.profiler_aggregate = 0" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.profiler_append = 0" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.profiler_enable = 0" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.profiler_enable_trigger = 0" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.profiler_enable_trigger_value" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.profiler_output_dir = /tmp/profile" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.profiler_output_name = cachegrind.out.%p" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.oaddr_header" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_autostart = 1" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_connect_back = 1" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_cookie_expire_time = 3600" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_enable = 1" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_handler = dbgp" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && /sbin/ip route|awk '/default/ { print $3 }' | xargs echo "xdebug.remote_host =$1" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_log" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_mode = req" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_port = 9009" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.scream = 0" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.show_error_trace = 1" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.show_exception_trace = 1" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.show_local_vars = 0" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.show_mem_delta = 1" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.trace_enable_trigger = 0" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.trace_enable_trigger_value" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.trace_format = 0" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.trace_options = 0" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.trace_output_dir = /tmp/xdebug" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.trace_output_name = trace.%c" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.var_display_max_children = -1" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.var_display_max_data = -1" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.var_display_max_depth = -1" >> /usr/local/etc/php/conf.d/xdebug.ini