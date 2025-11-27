--
-- PostgreSQL database dump
--

-- Dumped from database version 16.10 (Debian 16.10-1.pgdg13+1)
-- Dumped by pg_dump version 16.10 (Ubuntu 16.10-0ubuntu0.24.04.1)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: appointments; Type: TABLE; Schema: public; Owner: hospital_owner
--

CREATE TABLE public.appointments (
    id integer NOT NULL,
    patient_id integer,
    appointment_date timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    comment text,
    doctor_id integer NOT NULL
);


ALTER TABLE public.appointments OWNER TO hospital_owner;

--
-- Name: appointments_id_seq; Type: SEQUENCE; Schema: public; Owner: hospital_owner
--

CREATE SEQUENCE public.appointments_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.appointments_id_seq OWNER TO hospital_owner;

--
-- Name: appointments_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: hospital_owner
--

ALTER SEQUENCE public.appointments_id_seq OWNED BY public.appointments.id;


--
-- Name: doctors; Type: TABLE; Schema: public; Owner: hospital_owner
--

CREATE TABLE public.doctors (
    id integer NOT NULL,
    first_name character varying(100) NOT NULL,
    last_name character varying(100) NOT NULL,
    specialization character varying(100) NOT NULL,
    work_start time without time zone,
    work_end time without time zone,
    docloginid character varying(50),
    password character varying(255)
);


ALTER TABLE public.doctors OWNER TO hospital_owner;

--
-- Name: doctors_id_seq; Type: SEQUENCE; Schema: public; Owner: hospital_owner
--

CREATE SEQUENCE public.doctors_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.doctors_id_seq OWNER TO hospital_owner;

--
-- Name: doctors_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: hospital_owner
--

ALTER SEQUENCE public.doctors_id_seq OWNED BY public.doctors.id;


--
-- Name: medical_records; Type: TABLE; Schema: public; Owner: hospital_owner
--

CREATE TABLE public.medical_records (
    id integer NOT NULL,
    patient_id integer NOT NULL,
    doctor_id integer NOT NULL,
    appointment_id integer,
    event text NOT NULL,
    diagnosis text NOT NULL,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.medical_records OWNER TO hospital_owner;

--
-- Name: medical_records_id_seq; Type: SEQUENCE; Schema: public; Owner: hospital_owner
--

CREATE SEQUENCE public.medical_records_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.medical_records_id_seq OWNER TO hospital_owner;

--
-- Name: medical_records_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: hospital_owner
--

ALTER SEQUENCE public.medical_records_id_seq OWNED BY public.medical_records.id;


--
-- Name: patients; Type: TABLE; Schema: public; Owner: hospital_owner
--

CREATE TABLE public.patients (
    id integer NOT NULL,
    first_name character varying(100) NOT NULL,
    last_name character varying(100) NOT NULL,
    email character varying(150) NOT NULL,
    personal_code character varying(20) NOT NULL,
    password character varying(255) NOT NULL,
    phone character varying(30),
    gender character varying(20),
    medical_history text
);


ALTER TABLE public.patients OWNER TO hospital_owner;

--
-- Name: patients_id_seq; Type: SEQUENCE; Schema: public; Owner: hospital_owner
--

CREATE SEQUENCE public.patients_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.patients_id_seq OWNER TO hospital_owner;

--
-- Name: patients_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: hospital_owner
--

ALTER SEQUENCE public.patients_id_seq OWNED BY public.patients.id;


--
-- Name: appointments id; Type: DEFAULT; Schema: public; Owner: hospital_owner
--

ALTER TABLE ONLY public.appointments ALTER COLUMN id SET DEFAULT nextval('public.appointments_id_seq'::regclass);


--
-- Name: doctors id; Type: DEFAULT; Schema: public; Owner: hospital_owner
--

ALTER TABLE ONLY public.doctors ALTER COLUMN id SET DEFAULT nextval('public.doctors_id_seq'::regclass);


--
-- Name: medical_records id; Type: DEFAULT; Schema: public; Owner: hospital_owner
--

ALTER TABLE ONLY public.medical_records ALTER COLUMN id SET DEFAULT nextval('public.medical_records_id_seq'::regclass);


--
-- Name: patients id; Type: DEFAULT; Schema: public; Owner: hospital_owner
--

ALTER TABLE ONLY public.patients ALTER COLUMN id SET DEFAULT nextval('public.patients_id_seq'::regclass);


--
-- Data for Name: appointments; Type: TABLE DATA; Schema: public; Owner: hospital_owner
--

COPY public.appointments (id, patient_id, appointment_date, comment, doctor_id) FROM stdin;
1	2	2026-07-30 11:29:00	Recepto pratęsimas	4
2	2	2026-11-05 14:09:00	Trauma	5
3	2	2026-07-07 10:05:00	Recepto pratęsimas	9
4	2	2026-09-25 14:24:00	Profilaktinis patikrinimas	2
5	2	2026-12-30 07:36:00	Noriu pasikonsultuoti	6
6	3	2026-09-09 11:55:00	Recepto pratęsimas	8
7	3	2026-02-02 11:26:00	Trauma	10
8	3	2026-04-08 09:25:00	Reikalinga konsultacija	2
9	3	2026-04-16 11:35:00	Reikalinga konsultacija	4
10	3	2026-05-09 10:50:00	Recepto pratęsimas	6
11	3	2026-10-09 10:22:00	Noriu pasikonsultuoti	5
12	3	2026-07-05 11:40:00	Profilaktinis patikrinimas	11
13	3	2026-05-27 12:23:00	Skubus atvejis	6
14	3	2026-02-25 16:30:00	Noriu pasikonsultuoti	9
15	3	2026-03-13 15:21:00	Profilaktinis patikrinimas	7
16	1	2026-05-11 08:39:00	Trauma	1
17	1	2026-12-03 15:43:00	Reikalinga konsultacija	3
18	1	2026-08-28 12:58:00	Reikalinga konsultacija	9
19	1	2026-05-21 10:05:00	Skubus atvejis	9
20	1	2026-12-03 09:49:00	Recepto pratęsimas	6
21	1	2026-03-11 11:03:00	Noriu pasikonsultuoti	7
22	1	2026-01-19 09:08:00	Trauma	1
\.


--
-- Data for Name: doctors; Type: TABLE DATA; Schema: public; Owner: hospital_owner
--

COPY public.doctors (id, first_name, last_name, specialization, work_start, work_end, docloginid, password) FROM stdin;
1	Jonas	Petraitis	Kardiologas	08:00:00	16:00:00	doc01	$2y$12$HnjJyMge911d5eBCoqoAOe1HmgzjW06fwDV42p44.FkHq9q4WjJX2
2	Eglė	Bartkutė	Psichologas	09:00:00	17:00:00	doc02	$2y$12$KBDVN6mvN8mZ07ian9tPWemLnVeK3xIeqihVcnO9PUYgBZHakrqqS
3	Tomas	Kazlauskas	Pediatras	10:00:00	18:00:00	doc03	$2y$12$kYLa0dBtqBNQ7eMntj8BKODngVDzF4wBJ5fSBmdO/4KHlFhxZghQe
4	Monika	Stankevičienė	Odontologas	08:00:00	15:00:00	doc04	$2y$12$OH6E.F8Lj8Lt9GX0oH0JLOL8IH520u1wxjtelFBAKblPpsgLjhRCO
5	Andrius	Žukauskas	Dermatologas	09:30:00	17:30:00	doc05	$2y$12$421HqX7nLcLI0KQm7TCizOsLiXyNulQiQM.PGf4iYTKYC57uecGrq
6	Gintarė	Petrauskaitė	Ginekologas	07:30:00	14:30:00	doc06	$2y$12$RBkH/ncH0ejkdVG15im6D.J0JR0vpsSYkyE34RXbbALsqPWGXqpeq
7	Mantas	Vaitkus	Chirurgas	11:00:00	19:00:00	doc07	$2y$12$BFHXb9T7lx0Ko4K5/09yt.umLVZPuAmJH1d6q/F.njPbjRK.jWBhC
8	Rūta	Grigaitė	Chirurgas	08:30:00	16:30:00	doc08	$2y$12$99OW6zP.BmGvfAnALG4jwu2DSLDTllz/7Lhy9Xf2Rr1V4hkWiwqT.
9	Saulius	Jankauskas	Kardiologas	09:00:00	17:00:00	doc09	$2y$12$wcZVpkwFIrp6to5IbxtRwu.92dSvm4d6WZ1g.NFqoIGg6lnJbznEu
10	Mantas	Kavaliauskas	Šeimos daktaras	08:00:00	17:00:00	doc10	$2y$12$icPlp.hqjmnQoTZV9o6RK.VSGJ3XiOqUtL.lReCVg/CITRrDSkdPS
11	Mantė	Petrauskaitė	Odontologas	08:00:00	17:00:00	doc11	$2y$12$VtqV77Bql2.piaeb6GYeI.HMc7O6DmGEJpeU0.aOk/a.BC8uUSUhS
\.


--
-- Data for Name: medical_records; Type: TABLE DATA; Schema: public; Owner: hospital_owner
--

COPY public.medical_records (id, patient_id, doctor_id, appointment_id, event, diagnosis, created_at) FROM stdin;
1	2	4	1	Atvyko dėl periodinio patikrinimo	Radikulopatija	2026-07-30 11:29:00
2	2	5	2	Pacientas jaučia nugaros skausmus	Radikulopatija	2026-11-05 14:09:00
3	2	9	3	Pacientas skundžiasi galvos skausmu	Nėra ligos požymių	2026-07-07 10:05:00
4	2	2	4	Pacientas karščiuoja	Radikulopatija	2026-09-25 14:24:00
5	2	6	5	Pacientas jaučia silpnumą	Radikulopatija	2026-12-30 07:36:00
6	3	8	6	Atvyko dėl periodinio patikrinimo	Nėra ligos požymių	2026-09-09 11:55:00
7	3	10	7	Pacientas skundžiasi galvos skausmu	Migrena	2026-02-02 11:26:00
8	3	2	8	Atvyko dėl periodinio patikrinimo	Hipertenzija	2026-04-08 09:25:00
9	3	4	9	Atvyko po traumos	Migrena	2026-04-16 11:35:00
10	3	6	10	Pacientas skundžiasi galvos skausmu	Ūmus virusinis nazofaringitas (peršalimas)	2026-05-09 10:50:00
11	3	5	11	Pacientas jaučia silpnumą	Radikulopatija	2026-10-09 10:22:00
12	3	11	12	Atvyko po traumos	Gripas	2026-07-05 11:40:00
13	3	6	13	Reikalingas kraujo tyrimas	Ūmus virusinis nazofaringitas (peršalimas)	2026-05-27 12:23:00
14	3	9	14	Pacientas jaučia silpnumą	Ūmus virusinis nazofaringitas (peršalimas)	2026-02-25 16:30:00
15	3	7	15	Pacientas jaučia silpnumą	Ūmus virusinis nazofaringitas (peršalimas)	2026-03-13 15:21:00
16	1	1	16	Pacientas skundžiasi galvos skausmu	Nėra ligos požymių	2026-05-11 08:39:00
17	1	3	17	Reikalingas kraujo tyrimas	Gripas	2026-12-03 15:43:00
18	1	9	18	Reikalingas kraujo tyrimas	Gripas	2026-08-28 12:58:00
19	1	9	19	Atvyko po traumos	Alerginis rinitas	2026-05-21 10:05:00
20	1	6	20	Pacientas jaučia nugaros skausmus	Migrena	2026-12-03 09:49:00
21	1	7	21	Pacientas jaučia nugaros skausmus	Alerginis rinitas	2026-03-11 11:03:00
22	1	1	22	Pacientas jaučia silpnumą	Radikulopatija	2026-01-19 09:08:00
\.


--
-- Data for Name: patients; Type: TABLE DATA; Schema: public; Owner: hospital_owner
--

COPY public.patients (id, first_name, last_name, email, personal_code, password, phone, gender, medical_history) FROM stdin;
2	Gitanas	Nausėda	prezidentas@gov.lt	36405192222	$2y$12$BaeLdNsXC1u2oWIiO.ac9.mIoUCzsQx0iAPVgRYCTgkV04bVv1vWy	+37062222222	Vyras	\N
1	Egidijus	Dragūnass	selas@gmail.com	37604231111	$2y$12$8cnD7m2vrQP/w7hMJBbUM.ohf4kaIS5hLM4BcQLJ4dnRkQPUx24NS	+37061111111	Vyras	\N
3	Marytė	Pavardenė	maryte@yahoo.lt	48003159012	$2y$12$KCdItuwM71iyxnRaQwVfkuVZA9wAnhAyvpLpk3Qgdr0gwZsTklvoK	+37063333333	Moteris	\N
\.


--
-- Name: appointments_id_seq; Type: SEQUENCE SET; Schema: public; Owner: hospital_owner
--

SELECT pg_catalog.setval('public.appointments_id_seq', 25, true);


--
-- Name: doctors_id_seq; Type: SEQUENCE SET; Schema: public; Owner: hospital_owner
--

SELECT pg_catalog.setval('public.doctors_id_seq', 1, false);


--
-- Name: medical_records_id_seq; Type: SEQUENCE SET; Schema: public; Owner: hospital_owner
--

SELECT pg_catalog.setval('public.medical_records_id_seq', 22, true);


--
-- Name: patients_id_seq; Type: SEQUENCE SET; Schema: public; Owner: hospital_owner
--

SELECT pg_catalog.setval('public.patients_id_seq', 3, true);


--
-- Name: appointments appointments_pkey; Type: CONSTRAINT; Schema: public; Owner: hospital_owner
--

ALTER TABLE ONLY public.appointments
    ADD CONSTRAINT appointments_pkey PRIMARY KEY (id);


--
-- Name: doctors doctors_pkey; Type: CONSTRAINT; Schema: public; Owner: hospital_owner
--

ALTER TABLE ONLY public.doctors
    ADD CONSTRAINT doctors_pkey PRIMARY KEY (id);


--
-- Name: medical_records medical_records_pkey; Type: CONSTRAINT; Schema: public; Owner: hospital_owner
--

ALTER TABLE ONLY public.medical_records
    ADD CONSTRAINT medical_records_pkey PRIMARY KEY (id);


--
-- Name: patients patients_email_key; Type: CONSTRAINT; Schema: public; Owner: hospital_owner
--

ALTER TABLE ONLY public.patients
    ADD CONSTRAINT patients_email_key UNIQUE (email);


--
-- Name: patients patients_personal_code_key; Type: CONSTRAINT; Schema: public; Owner: hospital_owner
--

ALTER TABLE ONLY public.patients
    ADD CONSTRAINT patients_personal_code_key UNIQUE (personal_code);


--
-- Name: patients patients_pkey; Type: CONSTRAINT; Schema: public; Owner: hospital_owner
--

ALTER TABLE ONLY public.patients
    ADD CONSTRAINT patients_pkey PRIMARY KEY (id);


--
-- Name: appointments appointments_doctor_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: hospital_owner
--

ALTER TABLE ONLY public.appointments
    ADD CONSTRAINT appointments_doctor_id_fkey FOREIGN KEY (doctor_id) REFERENCES public.doctors(id);


--
-- Name: appointments appointments_patient_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: hospital_owner
--

ALTER TABLE ONLY public.appointments
    ADD CONSTRAINT appointments_patient_id_fkey FOREIGN KEY (patient_id) REFERENCES public.patients(id) ON DELETE CASCADE;


--
-- Name: medical_records medical_records_appointment_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: hospital_owner
--

ALTER TABLE ONLY public.medical_records
    ADD CONSTRAINT medical_records_appointment_id_fkey FOREIGN KEY (appointment_id) REFERENCES public.appointments(id) ON DELETE SET NULL;


--
-- Name: medical_records medical_records_doctor_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: hospital_owner
--

ALTER TABLE ONLY public.medical_records
    ADD CONSTRAINT medical_records_doctor_id_fkey FOREIGN KEY (doctor_id) REFERENCES public.doctors(id) ON DELETE CASCADE;


--
-- Name: medical_records medical_records_patient_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: hospital_owner
--

ALTER TABLE ONLY public.medical_records
    ADD CONSTRAINT medical_records_patient_id_fkey FOREIGN KEY (patient_id) REFERENCES public.patients(id) ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--