# Command to check if MySQL is ready
DB_CHECK_COMMAND=docker compose exec mysql mysqladmin ping -h mysql --silent

# Start only the database
db-start:
	docker compose up -d mysql
	@echo "⏳ Waiting for MySQL to be ready..."
	until $(DB_CHECK_COMMAND); do sleep 2; done
	@echo "✅ MySQL is ready!"

# Run migrations after ensuring DB is fully started
db-migrate: db-start api-start composer-install
	docker compose exec app php artisan migrate --seed
	@echo "✅ Database migrated successfully!"

composer-install:
	cp .env.example .env
	docker compose exec app composer install

# Generate API documentation after migrations
swagger-generate:
	docker compose exec app php artisan l5-swagger:generate
	@echo "✅ Swagger documentation generated!"

# Start the API after ensuring DB and migrations are done
api-start:
	docker compose up -d app nginx
	@echo "🚀 API is running at http://localhost:8080"

app-start: db-migrate
	@echo "⏳ Starting APP... at http://localhost:8080"

# Stop all running services
stop:
	docker compose down
	@echo "🛑 Application stopped."

# Restart everything (useful after changes)
restart: stop app-start
	@echo "🔄 Application restarted."

# Show running services
status:
	docker compose ps

# Show application logs
logs:
	docker compose logs -f

# Run Laravel commands inside the container
artisan:
	docker compose exec app php artisan $(cmd)

# Clean Docker (removes all containers and volumes)
clean:
	docker compose down -v
	@echo "🧹 All containers and volumes removed."
