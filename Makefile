.PHONY:
up:
	docker compose up -d

.PHONY:
down:
	docker compose down

.PHONY:
log:
	docker compose logs -f

.PHONY:
ps:
	docker compose ps

.PHONY:
bash:
	docker compose exec -it app bash
