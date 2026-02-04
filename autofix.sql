-- Adminer 5.4.1 PostgreSQL 15.15 dump

DROP DATABASE IF EXISTS "autofix";
CREATE DATABASE "autofix";
\connect "autofix";

DROP TABLE IF EXISTS "categories";
DROP SEQUENCE IF EXISTS categories_id_seq;
CREATE SEQUENCE categories_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 2147483647 CACHE 1;

CREATE TABLE "public"."categories" (
    "id" integer DEFAULT nextval('categories_id_seq') NOT NULL,
    "name" character varying(100),
    CONSTRAINT "categories_pkey" PRIMARY KEY ("id")
)
WITH (oids = false);

INSERT INTO "categories" ("id", "name") VALUES
(1,	'Uniwersalne'),
(2,	'Silnik'),
(3,	'Hamulce'),
(4,	'Elektryka'),
(5,	'Toyota'),
(6,	'BMW'),
(7,	'Volkswagen');

DROP TABLE IF EXISTS "comments";
DROP SEQUENCE IF EXISTS comments_id_seq;
CREATE SEQUENCE comments_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 2147483647 CACHE 1;

CREATE TABLE "public"."comments" (
    "id" integer DEFAULT nextval('comments_id_seq') NOT NULL,
    "content" text NOT NULL,
    "user_id" integer,
    "guide_id" integer,
    "created_at" timestamp DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT "comments_pkey" PRIMARY KEY ("id")
)
WITH (oids = false);

INSERT INTO "comments" ("id", "content", "user_id", "guide_id", "created_at") VALUES
(15,	'To jest Komentarz',	3,	12,	'2026-02-04 05:27:57.853522');

DROP TABLE IF EXISTS "guide_categories";
CREATE TABLE "public"."guide_categories" (
    "guide_id" integer NOT NULL,
    "category_id" integer NOT NULL,
    CONSTRAINT "guide_categories_pkey" PRIMARY KEY ("guide_id", "category_id")
)
WITH (oids = false);

INSERT INTO "guide_categories" ("guide_id", "category_id") VALUES
(13,	1),
(13,	5),
(14,	5),
(14,	6),
(12,	3),
(12,	6);

DROP TABLE IF EXISTS "guides";
DROP SEQUENCE IF EXISTS guides_id_seq;
CREATE SEQUENCE guides_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 2147483647 CACHE 1;

CREATE TABLE "public"."guides" (
    "id" integer DEFAULT nextval('guides_id_seq') NOT NULL,
    "title" character varying(255),
    "description" text,
    "user_id" integer,
    "created_at" timestamp DEFAULT CURRENT_TIMESTAMP,
    "image" character varying(255),
    "content" text,
    CONSTRAINT "guides_pkey" PRIMARY KEY ("id")
)
WITH (oids = false);

INSERT INTO "guides" ("id", "title", "description", "user_id", "created_at", "image", "content") VALUES
(13,	'To jest drugi Poradnik',	NULL,	3,	'2026-02-04 05:24:15.119375',	NULL,	'<p>Kolejny test</p>'),
(14,	'To jest kolejny poradnik',	NULL,	3,	'2026-02-04 05:24:32.919648',	'6982d888d2aa7.jpg',	'<p>Następny test</p>'),
(12,	'To jest pierwszy poradnik',	NULL,	3,	'2026-02-04 05:23:51.370781',	'6982d892b6e8c.jpg',	'<p>To jest test</p><p>&nbsp;</p><p>&nbsp;</p><p>To jest obraz w tekście</p><figure class="image"><img src="/uploads/6982d910a425b.png"></figure>');

DROP TABLE IF EXISTS "ratings";
DROP SEQUENCE IF EXISTS ratings_id_seq;
CREATE SEQUENCE ratings_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 2147483647 CACHE 1;

CREATE TABLE "public"."ratings" (
    "id" integer DEFAULT nextval('ratings_id_seq') NOT NULL,
    "user_id" integer,
    "guide_id" integer,
    "rating" integer,
    CONSTRAINT "ratings_pkey" PRIMARY KEY ("id"),
    CONSTRAINT "ratings_rating_check" CHECK (((rating >= 1) AND (rating <= 5)))
)
WITH (oids = false);

CREATE UNIQUE INDEX ratings_user_id_guide_id_key ON public.ratings USING btree (user_id, guide_id);

INSERT INTO "ratings" ("id", "user_id", "guide_id", "rating") VALUES
(13,	3,	12,	5);

DROP TABLE IF EXISTS "roles";
DROP SEQUENCE IF EXISTS roles_id_seq;
CREATE SEQUENCE roles_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 2147483647 CACHE 1;

CREATE TABLE "public"."roles" (
    "id" integer DEFAULT nextval('roles_id_seq') NOT NULL,
    "name" character varying(50),
    CONSTRAINT "roles_pkey" PRIMARY KEY ("id")
)
WITH (oids = false);

CREATE UNIQUE INDEX roles_name_key ON public.roles USING btree (name);

INSERT INTO "roles" ("id", "name") VALUES
(1,	'user'),
(2,	'moderator'),
(3,	'admin');

DROP TABLE IF EXISTS "users";
DROP SEQUENCE IF EXISTS users_id_seq;
CREATE SEQUENCE users_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 2147483647 CACHE 1;

CREATE TABLE "public"."users" (
    "id" integer DEFAULT nextval('users_id_seq') NOT NULL,
    "username" character varying(50),
    "email" character varying(100),
    "password_hash" text,
    "role_id" integer,
    "created_at" timestamp DEFAULT CURRENT_TIMESTAMP,
    "is_active" boolean DEFAULT true,
    CONSTRAINT "users_pkey" PRIMARY KEY ("id")
)
WITH (oids = false);

CREATE UNIQUE INDEX users_email_key ON public.users USING btree (email);

INSERT INTO "users" ("id", "username", "email", "password_hash", "role_id", "created_at", "is_active") VALUES
(3,	'Admin',	'mkr@test.com',	'$2y$10$fkha70WQ7vadmi.Te6rlS.tE.ywVjjDLbsBUf3ow7AH2npkT/nEdG',	3,	'2026-02-04 03:46:27.739294',	'1'),
(2,	'mktest',	'tet@t.com',	'$2y$10$hXt8iuCh9ChBgCGbwIV2aue3b091hvICzzyFf5qVscsauNqdbMrKe',	1,	'2026-02-03 23:38:47.895806',	'1'),
(4,	'troool',	'tete@t.com',	'$2y$10$4qB9OXTzNJfTBmwIT4MbFuuvzJWAOgoxGUJCUUTSbchQXHvfo0V2m',	1,	'2026-02-04 05:14:01.538122',	'1');

ALTER TABLE ONLY "public"."comments" ADD CONSTRAINT "comments_guide_id_fkey" FOREIGN KEY (guide_id) REFERENCES guides(id) ON DELETE CASCADE NOT DEFERRABLE;
ALTER TABLE ONLY "public"."comments" ADD CONSTRAINT "comments_user_id_fkey" FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE NOT DEFERRABLE;

ALTER TABLE ONLY "public"."guide_categories" ADD CONSTRAINT "guide_categories_category_id_fkey" FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE NOT DEFERRABLE;
ALTER TABLE ONLY "public"."guide_categories" ADD CONSTRAINT "guide_categories_guide_id_fkey" FOREIGN KEY (guide_id) REFERENCES guides(id) ON DELETE CASCADE NOT DEFERRABLE;

ALTER TABLE ONLY "public"."guides" ADD CONSTRAINT "guides_user_id_fkey" FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE NOT DEFERRABLE;

ALTER TABLE ONLY "public"."ratings" ADD CONSTRAINT "ratings_guide_id_fkey" FOREIGN KEY (guide_id) REFERENCES guides(id) ON DELETE CASCADE NOT DEFERRABLE;
ALTER TABLE ONLY "public"."ratings" ADD CONSTRAINT "ratings_user_id_fkey" FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE NOT DEFERRABLE;

ALTER TABLE ONLY "public"."users" ADD CONSTRAINT "users_role_id_fkey" FOREIGN KEY (role_id) REFERENCES roles(id) NOT DEFERRABLE;

-- 2026-02-04 06:09:39 UTC