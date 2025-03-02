# **Airplane Ticket Reservation API**

## **📌 Description**
This project is a **minimal Airplane Ticket Reservation System** built with **Laravel** and **Docker**. It provides a **RESTful API** to:

- **Book airplane tickets**
- **Cancel tickets**
- **Change seats**
- **Validate airport codes** (airports are preloaded for validation)
- **Generate API documentation with Swagger**

The project is **fully containerized** using **Docker Compose**, ensuring a seamless development experience.

---

## **🚀 How to Start the Project**

### **1️⃣ Prerequisites**
- **Docker (latest version)** installed
- **Make (GNU Make)** installed
- **Git** installed

### **2️⃣ Clone the Repository**
```sh
 git clone https://github.com/PaulElMaaouchi/AirplaneTIcket.git
 cd airplane-ticket-reservation
```

### **3️⃣ Setup and Start the Application**
To start the full application, simply run:
```sh
 make app-start
```
➡ **This will:**
- Start the MySQL database 🗄️
- Run database migrations & seed default airports ✈️
- Generate API documentation 📜
- Start the Laravel API and Nginx 🚀

Once complete, the API will be available at:
```
http://localhost:8080
```
Swagger API documentation:
```
http://localhost:8080/api/documentation
```

### **4️⃣ Stopping the Application**
To stop all running services, use:
```sh
make stop
```

### **5️⃣ Restarting the Application**
To restart everything (useful after code changes):
```sh
make restart
```

### **6️⃣ Checking Running Services**
```sh
make status
```

### **7️⃣ Viewing Logs**
```sh
make logs
```

### **8️⃣ Running Laravel Commands Inside the Container**
You can execute **Laravel Artisan commands** using:
```sh
make artisan cmd=migrate
```
Examples:
```sh
make artisan cmd=config:clear
make artisan cmd=cache:clear
make artisan cmd=route:list
```

### **9️⃣ Cleaning Up (Removing Containers and Volumes)**
```sh
make clean
```
🚨 **Warning:** This will **delete all database data**.

---

## **🔧 Makefile Details**
This project uses a **Makefile** for automation. The Makefile assumes you have the **latest Docker version**, where `docker compose` is used instead of `docker-compose`.

### **⚠️ If You Are Using an Old Docker Version**
Change all occurrences of `docker compose` to `docker-compose` inside the **Makefile**.

### **📜 Makefile Commands Overview**
```makefile
# Command to check if MySQL is ready
DB_CHECK_COMMAND=docker compose exec mysql mysqladmin ping -h mysql --silent

# Start only the database
db-start:
	docker compose up -d mysql
	@echo "⏳ Waiting for MySQL to be ready..."
	until $(DB_CHECK_COMMAND); do sleep 2; done
	@echo "✅ MySQL is ready!"

# Run migrations after ensuring DB is fully started
db-migrate: db-start api-start
	docker compose exec app php artisan migrate --seed
	@echo "✅ Database migrated successfully!"

# Generate API documentation after migrations
swagger-generate:
	docker compose exec app php artisan l5-swagger:generate
	@echo "✅ Swagger documentation generated!"

# Start the API after ensuring DB and migrations are done
api-start:
	docker compose up -d app nginx
	@echo "🚀 API is running at http://localhost:8080"

# Start the entire app
app-start: db-migrate
	@echo "⏳ Starting APP... at http://localhost:8080"

# Stop all running services
stop:
	docker compose down
	@echo "🛑 Application stopped."

# Restart everything
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
```

---

## **📌 API Features**
### ✅ **1. Book a Ticket**
**Endpoint:** `POST /api/tickets`
```sh
curl -X POST http://localhost:8080/api/tickets -H "Content-Type: application/json" -d '{
  "passport_id": "A1234567",
  "source_airport": "JFK",
  "destination_airport": "LAX",
  "departure_time": "2025-03-10T12:00:00",
  "aircraft_number": "AA101"
}'
```
### ✅ **2. Cancel a Ticket**
**Endpoint:** `PATCH /api/tickets/{id}/cancel`
```sh
curl -X PATCH http://localhost:8080/api/tickets/1/cancel
```
### ✅ **3. Change Seat**
**Endpoint:** `PATCH /api/tickets/{id}/seat`
```sh
curl -X PATCH http://localhost:8080/api/tickets/1/seat
```
### ✅ **4. Fetch All Airports**
**Endpoint:** `GET /api/airports`
```sh
curl -X GET http://localhost:8080/api/airports
```

---

## **📜 License**
This project is open-source and available under the **MIT License**.

🚀 **Now You're Ready to Run the Airplane Ticket Reservation System!**

